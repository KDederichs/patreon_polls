<?php

namespace App\Service;

use App\Entity\PatreonCampaign;
use App\Entity\PatreonCampaignTier;
use App\Entity\User;
use App\Repository\PatreonCampaignRepository;
use App\Repository\PatreonCampaignTierRepository;
use App\Repository\UserRepository;
use Carbon\CarbonImmutable;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class PatreonService
{
    public function __construct(
        private readonly HttpClientInterface $client,
        #[Autowire(env: 'PATREON_ID')] private readonly string $patreonClientId,
        #[Autowire(env: 'PATREON_SECRET')] private readonly string $patreonClientSecret,
        private UserRepository $userRepository,
        private PatreonCampaignRepository $campaignRepository,
        private PatreonCampaignTierRepository $campaignTierRepository,
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

    public function refreshCampaigns(User $user): void
    {
        $this->refreshAccessToken($user);
        $payload = [
            'include' => 'tiers',
            'fields[campaign]' => 'creation_name',
            'fields[tier]' => 'title'
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
        $includes = $responsePayload['included'];
        foreach ($responsePayload['data'] as $campaignData) {
            $campaign = $this->campaignRepository->findByPatreonCampaignId($campaignData['id']);
            if (!$campaign) {
                $campaign = new PatreonCampaign();
                $campaign
                    ->setCampaignOwner($user)
                    ->setPatreonCampaignId($campaignData['id']);
                $this->campaignRepository->persist($campaign);
            }

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
                        $patreonTier->setTierName($include['attributes']['title']);
                    }
                }
            }
        }
        $this->campaignRepository->save();
    }
}
