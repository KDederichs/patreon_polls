<?php

namespace App\Service;

use App\Entity\PatreonCampaign;
use App\Entity\PatreonCampaignTier;
use App\Entity\PatreonCampaignWebhook;
use App\Entity\PatreonUser;
use App\Message\FetchCampaignMembersMessage;
use App\Repository\PatreonCampaignRepository;
use App\Repository\PatreonCampaignTierRepository;
use App\Repository\PatreonCampaignWebhookRepository;
use App\Repository\PatreonUserRepository;
use App\Service\Oauth\PatreonOAuthService;
use Carbon\CarbonImmutable;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class PatreonService implements LoggerAwareInterface
{
    private ?LoggerInterface $logger = null;

    public function __construct(
        private readonly HttpClientInterface              $client,
        private readonly PatreonCampaignRepository        $campaignRepository,
        private readonly PatreonCampaignTierRepository    $campaignTierRepository,
        private readonly PatreonCampaignWebhookRepository $campaignWebhookRepository,
        private readonly RouterInterface                  $router,
        private readonly MessageBusInterface              $bus,
        private readonly PatreonUserRepository            $patreonUserRepository,
        private readonly PatreonOAuthService              $patreonOAuth,
    ) {
    }

    public function refreshAccessToken(PatreonUser $patreonUser): void
    {
        if ($patreonUser->getAccessTokenExpiresAt()?->subDays(2)->isPast()) {
            $oauthToken = $this->patreonOAuth->refreshAccessToken($patreonUser->getRefreshToken());
            $patreonUser
                ->setScope($oauthToken?->getScope())
                ->setRefreshToken($oauthToken?->getRefreshToken())
                ->setAccessTokenExpiresAt($oauthToken ? CarbonImmutable::now()->addSeconds($oauthToken->getExpiresIn()) : null)
                ->setAccessToken($oauthToken?->getAccessToken())
                ->setTokenType($oauthToken?->getTokenType());
            $this->patreonUserRepository->save();
        }
    }

    public function refreshCampaigns(PatreonUser $patreonUser): array
    {
        $payload = [
            'include' => 'tiers',
            'fields[campaign]' => 'creation_name',
            'fields[tier]' => 'title,amount_cents'
        ];
        $response = $this->client->request(
            'GET',
            "https://www.patreon.com/api/oauth2/v2/campaigns?".http_build_query($payload),
            [
                'headers' => [
                    'Accept' => 'application/json',
                    'Authorization' => $patreonUser->getTokenType().' ' . $patreonUser->getAccessToken(),
                ]
            ]
        );
        $responsePayload = json_decode($response->getContent(), true);
        $includes = $responsePayload['included'];
        $campaigns = [];
        foreach ($responsePayload['data'] as $campaignData) {
            $campaign = $this->campaignRepository->findByPatreonCampaignId($campaignData['id']);
            if (!$campaign) {
                $campaign = new PatreonCampaign();
                $campaign
                    ->setCampaignOwner($patreonUser->getUser())
                    ->setOwner($patreonUser)
                    ->setPatreonCampaignId($campaignData['id']);
                $this->campaignRepository->persist($campaign);

            }
            $campaigns[] = $campaign;
            $campaign
                ->setCampaignName($campaignData['attributes']['creation_name']);

            $tiers = $campaignData['relationships']['tiers']['data'] ?? [];

            foreach ($tiers as $tier) {
                $patreonTierId = $tier['id'];
                $patreonTier = $this->campaignTierRepository->findByPatreonTierId($patreonTierId);
                if (!$patreonTier) {
                    $patreonTier = new PatreonCampaignTier();
                    $patreonTier
                        ->setCampaign($campaign)
                        ->setPatreonTierId($patreonTierId);
                    $this->campaignRepository->persist($patreonTier);
                }
                foreach ($includes as $include) {
                    if ($include['type'] !== 'tier') {
                        continue;
                    }

                    if ($include['id'] === $patreonTierId) {
                        $patreonTier
                            ->setTierName($include['attributes']['title'])
                            ->setAmountInCents($include['attributes']['amount_cents'])
                        ;
                    }
                }
            }
        }
        $this->campaignRepository->save();
        return $campaigns;
    }

    public function fetchCampaignMembers(PatreonCampaign $campaign): void
    {
        $this->bus->dispatch(new FetchCampaignMembersMessage($campaign->getId()));
    }

    public function doFetchMembersRequest(PatreonCampaign $campaign, ?string $cursor = null): array
    {
        $user = $campaign->getOwner();
        $this->refreshAccessToken($user);

        $queryParams = [
            'include' => 'currently_entitled_tiers,user'
        ];

        if ($cursor) {
            $queryParams['page[cursor]'] = $cursor;
        }

        $response = $this->client->request(
            'GET',
            "https://www.patreon.com/api/oauth2/v2/campaigns/".$campaign->getPatreonCampaignId()."/members?".http_build_query($queryParams),
            [
                'headers' => [
                    'Accept' => 'application/json',
                    'Authorization' => $user->getTokenType().' ' . $user->getAccessToken(),
                ]
            ]
        );

        return json_decode($response->getContent(), true);
    }

    public function enableMemberUpdateWebhook(PatreonCampaign $campaign): void
    {
        /** @var PatreonCampaign $dbCampaign */
        $dbCampaign = $this->campaignRepository->find($campaign->getId());
        $webhook = $this->campaignWebhookRepository->findByCampaign($dbCampaign);
        if ($webhook) {
            return;
        }
        $user = $dbCampaign->getOwner();
        $this->refreshAccessToken($user);
        $uri = $this->router->generate('patreon_webhooks',[], UrlGeneratorInterface::ABSOLUTE_URL);
        $uri = str_replace('http://','https://', $uri);
        if (str_contains($uri, 'localhost')) {
            return;
        }
        $payload = [
            'data' => [
                'type' => 'webhook',
                'attributes' => [
                    'triggers' => [
                        'members:pledge:create',
                        'members:pledge:update',
                        'members:pledge:delete',
                    ],
                    'uri' => $uri
                ],
                'relationships' => [
                    'campaign' => [
                        'data' => [
                            'type' => 'campaign',
                            'id' => $dbCampaign->getPatreonCampaignId()
                        ]
                    ]
                ]
            ]
        ];
        $response = $this->client->request(
            'POST',
            "https://www.patreon.com/api/oauth2/v2/webhooks",
            [
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    'Authorization' => $user->getTokenType().' ' . $user->getAccessToken(),
                ],
                'json' => $payload
            ]
        );

        $decodedResponse = json_decode($response->getContent(), true);
        $webhookId = $decodedResponse['data']['id'] ?? null;

        if (!$webhookId) {
            $this->logger?->error('Error creating webhook: '.$response->getContent());
            return;
        }

        $patreonWebhook = new PatreonCampaignWebhook();
        $patreonWebhook
            ->setPatreonWebhookId($webhookId)
            ->setCampaign($dbCampaign)
            ->setSecret($decodedResponse['data']['attributes']['secret'])
            ->setTriggers($decodedResponse['data']['attributes']['triggers']);

        $this->campaignRepository->persist($patreonWebhook);
        $this->campaignRepository->save();
    }

    public function syncPatreon(PatreonUser $user): void
    {
        /** @var PatreonCampaign[] $campaigns */
        $campaigns = $this->refreshCampaigns($user);

        foreach ($campaigns as $campaign) {
            $this->fetchCampaignMembers($campaign);
            $this->enableMemberUpdateWebhook($campaign);
        }
    }

    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }
}
