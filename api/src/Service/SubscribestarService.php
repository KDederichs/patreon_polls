<?php

namespace App\Service;

use App\Dto\OAuth\OAuthIdentity;
use App\Entity\PatreonUser;
use App\Entity\SubscribestarSubscription;
use App\Entity\SubscribestarTier;
use App\Entity\SubscribestarUser;
use App\Repository\PatreonCampaignRepository;
use App\Repository\PatreonCampaignTierRepository;
use App\Repository\PatreonCampaignWebhookRepository;
use App\Repository\PatreonUserRepository;
use App\Repository\SubscribestarSubscriptionRepository;
use App\Repository\SubscribestarTierRepository;
use App\Repository\SubscribestarUserRepository;
use App\Service\Oauth\PatreonOAuthService;
use App\Service\Oauth\SubscribestarOAuthService;
use Carbon\CarbonImmutable;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class SubscribestarService implements LoggerAwareInterface
{
    private ?LoggerInterface $logger = null;
    private const string API_URL = 'https://www.subscribestar.com';

    public function __construct(
        private readonly HttpClientInterface              $client,
        private readonly SubscribestarTierRepository    $subscribestarTierRepository,
        private readonly SubscribestarUserRepository            $subscribestarUserRepository,
        private readonly SubscribestarOAuthService              $subscribestarOAuthService,
        private readonly SubscribestarSubscriptionRepository $subscribestarSubscriptionRepository
    ) {
    }

    public function refreshAccessToken(SubscribestarUser $subscribestarUser): void
    {
        if ($subscribestarUser->getAccessTokenExpiresAt()?->subDays(2)->isPast()) {
            $oauthToken = $this->subscribestarOAuthService->refreshAccessToken($subscribestarUser->getRefreshToken());
            $subscribestarUser
                ->setScope($oauthToken->getScope())
                ->setRefreshToken($oauthToken->getRefreshToken())
                ->setAccessTokenExpiresAt(CarbonImmutable::now()->addSeconds($oauthToken->getExpiresIn()))
                ->setAccessToken($oauthToken->getAccessToken())
                ->setTokenType($oauthToken->getTokenType());
            $this->subscribestarUserRepository->save();
        }
    }

    public function refreshTiers(SubscribestarUser $subscribestarUser): void
    {
        if (!$subscribestarUser->isCreator()) {
            return;
        }

        $tokenType = $subscribestarUser->getTokenType();
        $accessToken = $subscribestarUser->getAccessToken();

        try {
            $response = $this->client->request(
                'POST',
                sprintf('%s/api/graphql/v1', self::API_URL),
                [
                    'headers' => [
                        'Authorization' => sprintf('%s %s', $tokenType, $accessToken),
                        'Content-Type' => 'application/json',
                        'Accept' => 'application/json',
                    ],
                    'json' => [
                        'query' => "{
                            content_provider_profile {
                                tiers {
                                  nodes {
                                    id
                                    title
                                    cost
                                  }
                                }
                            }
                        }"
                    ]
                ]
            );

            if ($response->getStatusCode() > 299) {
                $this->logger?->error(sprintf('Error fetching subscribestar tiers: %s', $response->getContent()));
                return;
            }

            $decoded = json_decode($response->getContent(), true, 512, JSON_THROW_ON_ERROR);
            foreach ($decoded['data']['content_provider_profile']['tiers']['nodes'] as $tierData) {
                $tier = $this->subscribestarTierRepository->findBySubscribestarTierId($tierData['id']);
                if (!$tier) {
                    $tier = new SubscribestarTier();
                    $tier
                        ->setSubscribestarUser($subscribestarUser)
                        ->setSubscribestarTierId($tierData['id'])
                        ->setTierName($tierData['title'])
                        ->setAmountInCents($tierData['cost']);
                    $this->subscribestarTierRepository->persist($tier);

                    foreach ($this->subscribestarSubscriptionRepository->findForTierId($tierData['id']) as $subscription) {
                        $subscription
                            ->setSubscribedTo($subscribestarUser)
                            ->setSubscribestarTier($tier);
                    }
                } else {
                    $tier
                        ->setTierName($tierData['title'])
                        ->setAmountInCents($tierData['cost']);
                }
            }
            $this->subscribestarTierRepository->save();
        } catch (TransportExceptionInterface|\JsonException $e) {
            $this->logger?->error(sprintf('Error fetching subscribestar tiers: %s', $e->getMessage()));
        }
    }

    public function getSubscriptions(SubscribestarUser $subscribestarUser): void
    {
        $tokenType = $subscribestarUser->getTokenType();
        $accessToken = $subscribestarUser->getAccessToken();
        try {
            $response = $this->client->request(
                'POST',
                sprintf('%s/api/graphql/v1', self::API_URL),
                [
                    'headers' => [
                        'Authorization' => sprintf('%s %s', $tokenType, $accessToken),
                        'Content-Type' => 'application/json',
                        'Accept' => 'application/json',
                    ],
                    'json' => [
                        'query' => "{
                            user {
                                subscriptions {
                                  nodes {
                                    id
                                    tier_id
                                    content_provider_profile {
                                      id
                                    }
                                    active
                                  }
                                }
                          }
                        }"
                    ]
                ]
            );

            if ($response->getStatusCode() > 299) {
                $this->logger?->error(sprintf('Error fetching subscribestar subscriptions: %s', $response->getContent()));
                return;
            }

            $decoded = json_decode($response->getContent(), true, 512, JSON_THROW_ON_ERROR);
            foreach ($decoded['data']['user']['subscriptions']['nodes'] as $subscriptionData) {
                $subscription = $this->subscribestarSubscriptionRepository->findBySubscribestarId($subscriptionData['id']);
                if (!$subscription) {
                    $subscription = new SubscribestarSubscription();
                    $subscription
                        ->setSubscribestarId($subscriptionData['id'])
                        ->setSubscribestarUser($subscribestarUser)
                        ->setTierId($subscriptionData['tier_id'])
                        ->setActive($subscriptionData['active'])
                        ->setContentProviderId($subscriptionData['content_provider_profile']['id']);
                    $this->subscribestarSubscriptionRepository->persist($subscription);
                }

                if (!$subscription->getSubscribestarTier()) {
                    $tier = $this->subscribestarTierRepository->findBySubscribestarTierId($subscriptionData['tier_id']);
                    $subscription
                        ->setSubscribedTo($tier?->getSubscribestarUser())
                        ->setSubscribestarTier($tier);
                }

                $subscription->setActive($subscriptionData['active']);
            }
            $this->subscribestarSubscriptionRepository->save();
        } catch (TransportExceptionInterface|\JsonException $e) {
            $this->logger?->error(sprintf('Error fetching subscribestar subscriptions: %s', $e->getMessage()));
        }
    }

    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }
}
