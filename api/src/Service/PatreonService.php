<?php

namespace App\Service;

use App\Entity\PatreonCampaign;
use App\Entity\PatreonCampaignTier;
use App\Entity\PatreonCampaignWebhook;
use App\Entity\User;
use App\Message\FetchCampaignMembersMessage;
use App\Repository\PatreonCampaignRepository;
use App\Repository\PatreonCampaignTierRepository;
use App\Repository\PatreonCampaignWebhookRepository;
use App\Repository\UserRepository;
use Carbon\CarbonImmutable;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class PatreonService implements LoggerAwareInterface
{
    private ?LoggerInterface $logger = null;

    public function __construct(
        private readonly HttpClientInterface $client,
        #[Autowire(env: 'PATREON_ID')] private readonly string $patreonClientId,
        #[Autowire(env: 'PATREON_SECRET')] private readonly string $patreonClientSecret,
        private readonly UserRepository $userRepository,
        private readonly PatreonCampaignRepository $campaignRepository,
        private readonly PatreonCampaignTierRepository $campaignTierRepository,
        private readonly PatreonCampaignWebhookRepository $campaignWebhookRepository,
        private readonly RouterInterface $router,
        private readonly MessageBusInterface $bus,
    ) {
    }

    public function refreshAccessToken(User $user): void
    {
        if ($user->getPatreonTokenExpiresAt()?->isPast()) {
            $refreshToken = $user->getPatreonRefreshToken();
            $clientId = $this->patreonClientId;
            $clientSecret = $this->patreonClientSecret;
            $response = $this->client->request(
                'POST',
                "www.patreon.com/api/oauth2/token?grant_type=refresh_token&refresh_token=$refreshToken=&client_id=$clientId&client_secret=$clientSecret",
                [
                    'headers' => [
                        'Content-Type' => 'application/x-www-form-urlencoded',
                    ]
                ]
            );
            $content = $response->getContent();
            $payload = json_decode($content, true, 512, JSON_THROW_ON_ERROR);
            $user
                ->setPatreonScope($payload['scope'])
                ->setPatreonRefreshToken($payload['refresh_token'])
                ->setPatreonTokenExpiresAt(CarbonImmutable::now()->addSeconds($payload['expires_in']))
                ->setPatreonAccessToken($payload['access_token'])
                ->setPatreonTokenType($payload['token_type']);
            $this->userRepository->save();
        }
    }

    public function refreshCampaigns(User $user): array
    {
        $this->refreshAccessToken($user);
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
                    'Authorization' => $user->getPatreonTokenType().' ' . $user->getPatreonAccessToken(),
                ]
            ]
        );
        $responsePayload = json_decode($response->getContent(), true);
        $includes = $responsePayload['included'] ?? [];
        $campaigns = [];
        foreach ($responsePayload['data'] as $campaignData) {
            $campaign = $this->campaignRepository->findByPatreonCampaignId($campaignData['id']);
            if (!$campaign) {
                $campaign = new PatreonCampaign();
                $campaign
                    ->setCampaignOwner($user)
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

    public function doFetchMembersRequest(PatreonCampaign $campaign, string $cursor = null): array
    {
        $user = $campaign->getCampaignOwner();
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
                    'Authorization' => $user->getPatreonTokenType().' ' . $user->getPatreonAccessToken(),
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
        $user = $dbCampaign->getCampaignOwner();
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
                    'Authorization' => $user->getPatreonTokenType().' ' . $user->getPatreonAccessToken(),
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

    public function syncPatreon(User $user): void
    {
        /** @var PatreonCampaign[] $campaigns */
        $campaigns = $this->refreshCampaigns($user);

        foreach ($campaigns as $campaign) {
            $this->fetchCampaignMembers($campaign);
            $this->enableMemberUpdateWebhook($campaign);
        }
    }

    public function convertToCreatorAccount(User $user): void
    {
        /** @var PatreonCampaign[] $campaigns */
        $campaigns = $this->refreshCampaigns($user);

        foreach ($campaigns as $campaign) {
            $this->fetchCampaignMembers($campaign);
            $this->enableMemberUpdateWebhook($campaign);
        }
        $dbUser = $this->userRepository->find($user->getId());
        $this->userRepository->getEntityManager()->refresh($dbUser);
        $dbUser->setCreator(true);

        $this->userRepository->save();
    }

    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }
}
