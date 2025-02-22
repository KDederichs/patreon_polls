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

class PatreonOAuthService extends GenericOAuthService
{
    public function __construct(
        private readonly SerializerInterface $serializer,
        private readonly HttpClientInterface $client,
        private readonly RequestStack $requestStack,
        #[Autowire(env: 'PATREON_ID')] private readonly string $patreonClientId,
        #[Autowire(env: 'PATREON_SECRET')] private readonly string $patreonClientSecret,
    )
    {
        parent::__construct($this->serializer, $this->client, $this->requestStack);
    }

    private const string API_URL = 'https://www.patreon.com';

    protected function getAuthorizeUrl(): string
    {
        return sprintf('%s/oauth2/authorize', self::API_URL);
    }

    protected function getTokenUrl(): string
    {
        return sprintf('%s/api/oauth2/token', self::API_URL);
    }

    protected function getClientId(): string
    {
        return $this->patreonClientId;
    }

    protected function getClientSecret(): string
    {
        return $this->patreonClientSecret;
    }

    public function getIdentity(string $accessToken, string $tokenType): ?OAuthIdentity
    {
        try {
            $response = $this->client->request(
                'GET',
                sprintf('%s/api/oauth2/v2/identity?fields[user]=full_name', self::API_URL),
                [
                    'headers' => [
                        'Authorization' => sprintf('%s %s', $tokenType, $accessToken),
                    ],
                ]
            );

            if ($response->getStatusCode() > 299) {
                $this->logger?->error(sprintf('Error fetching patreon identity: %s', $response->getContent()));
                return null;
            }

            $decoded = json_decode($response->getContent(), true, 512, JSON_THROW_ON_ERROR);

            if (!isset($decoded['data']['id'])) {
                $this->logger?->error(sprintf('Missing patreon ID: %s', $response->getContent()));
                return null;
            }

            return new OAuthIdentity(
                $decoded['data']['id'],
                $decoded['data']['attributes']['full_name'] ?? null
            );

        } catch (TransportExceptionInterface|\JsonException $e) {
            $this->logger?->error(sprintf('Error fetching patreon identity: %s', $e->getMessage()));
            return null;
        }
    }

    protected function getScope(OauthState $oauthState): string
    {
        return match ($oauthState->getAuthType()) {
            OAuthAuthType::Login, OAuthAuthType::Connect => 'identity',
            OAuthAuthType::ConnectAsCreator => 'identity campaigns campaigns.members w:campaigns.webhook'
        };
    }
}
