<?php

namespace App\Service\Oauth;

use App\Dto\OAuth\OAuthIdentity;
use App\Entity\OauthState;
use App\Enum\OAuthAuthType;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class SubscribestarOAuthService extends GenericOAuthService
{

    public function __construct(
        private readonly SerializerInterface $serializer,
        private readonly HttpClientInterface $client,
        private readonly RequestStack $requestStack,
        #[Autowire(env: 'SUBSCRIBESTAR_ID')] private readonly string $subscribestarClientId,
        #[Autowire(env: 'SUBSCRIBESTAR_SECRET')] private readonly string $subscribestarClientSecret,
    )
    {
        parent::__construct($this->serializer, $this->client, $this->requestStack);
    }

    private const string API_URL = 'https://www.subscribestar.com';

    protected function getAuthorizeUrl(): string
    {
        return sprintf('%s/oauth2/authorize', self::API_URL);

    }

    protected function getTokenUrl(): string
    {
        return sprintf('%s/oauth2/token', self::API_URL);
    }

    protected function getClientId(): string
    {
        return $this->subscribestarClientId;
    }

    protected function getClientSecret(): string
    {
        return $this->subscribestarClientSecret;
    }

    public function getIdentity(string $accessToken, string $tokenType): ?OAuthIdentity
    {
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
                        'query' => "{user {name, id}}"
                    ]
                ]
            );

            if ($response->getStatusCode() > 299) {
                $this->logger?->error(sprintf('Error fetching subscribestar identity: %s', $response->getContent()));
                return null;
            }

            $decoded = json_decode($response->getContent(), true, 512, JSON_THROW_ON_ERROR);

            if (!isset($decoded['data']['user']['id'])) {
                $this->logger?->error(sprintf('Missing subscribestar ID: %s', $response->getContent()));
                return null;
            }

            return new OAuthIdentity(
                $decoded['data']['user']['id'],
                $decoded['data']['user']['name'] ?? null
            );

        } catch (TransportExceptionInterface|\JsonException $e) {
            $this->logger?->error(sprintf('Error fetching subscribestar identity: %s', $e->getMessage()));
            return null;
        }
    }

    protected function getScope(OauthState $oauthState): string
    {
        return match ($oauthState->getAuthType()) {
            OAuthAuthType::Login, OAuthAuthType::Connect => 'user.read+user.subscriptions.read',
            OAuthAuthType::ConnectAsCreator => 'user.read+user.subscriptions.read+content_provider_profile.read+content_provider_profile.subscriptions.read'
        };
    }
}
