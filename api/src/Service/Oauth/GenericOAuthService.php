<?php

namespace App\Service\Oauth;

use App\Dto\OAuth\OAuthAccessToken;
use App\Dto\OAuth\OAuthIdentity;
use App\Entity\OauthState;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

abstract class GenericOAuthService implements LoggerAwareInterface
{

    protected ?LoggerInterface $logger = null;

    public function __construct(
        private readonly SerializerInterface $serializer,
        private readonly HttpClientInterface $client,
        private readonly RequestStack $requestStack,
    )
    {

    }

    public function getOauthUrl(OauthState $oauthState): string
    {
        $queryData = [
            'response_type' => 'code',
            'client_id' => $this->getClientId(),
            'redirect_uri' => $this->getRedirectUri(),
            'scope' => $this->getScope($oauthState),
            'state' => $oauthState->getId()->toRfc4122(),
        ];

        return sprintf('%s?%s', $this->getAuthorizeUrl(), http_build_query($queryData));
    }

    public function getAccessToken(string $code): ?OAuthAccessToken
    {
        $queryData = [
            'code' => $code,
            'grant_type' => 'authorization_code',
            'client_id' => $this->getClientId(),
            'client_secret' => $this->getClientSecret(),
            'redirect_uri' => $this->getRedirectUri(),
        ];

        try {
            $response = $this->client->request(
                'POST',
                $this->getTokenUrl(),
                [
                    'headers' => [
                        'Content-Type' => 'application/x-www-form-urlencoded',
                    ],
                    'body' => $queryData
                ]
            );

            if ($response->getStatusCode() > 299) {
                $this->logger?->error(sprintf('Error getting access token for url %s: %s', $this->getTokenUrl(), $response->getContent()));
                return null;
            }
            return $this->serializer->deserialize($response->getContent(), OAuthAccessToken::class,'json');
        } catch (TransportExceptionInterface $e) {
            $this->logger?->error(sprintf('Error getting access token for url %s: %s', $this->getTokenUrl(), $e->getMessage()));
            return null;
        }
    }

    public function refreshAccessToken(string $refreshToken):? OAuthAccessToken
    {
        $queryData = [
            'refresh_token' => $refreshToken,
            'grant_type' => 'refresh_token',
            'client_id' => $this->getClientId(),
            'client_secret' => $this->getClientSecret(),
        ];

        try {
            $response = $this->client->request(
                'POST',
                $this->getTokenUrl(),
                [
                    'headers' => [
                        'Content-Type' => 'application/x-www-form-urlencoded',
                    ],
                    'body' => $queryData
                ]
            );

            if ($response->getStatusCode() > 299) {
                $this->logger?->error(sprintf('Error getting refresh token for url %s: %s', $this->getTokenUrl(), $response->getContent()));
                return null;
            }
            return $this->serializer->deserialize($response->getContent(), OAuthAccessToken::class,'json');
        } catch (TransportExceptionInterface $e) {
            $this->logger?->error(sprintf('Error getting refresh token for url %s: %s', $this->getTokenUrl(), $e->getMessage()));
            return null;
        }
    }

    protected function getRedirectUri(): string
    {
        $request = $this->requestStack->getMainRequest();
        if (!$request) {
            throw new \LogicException('There has to be a request');
        }

        $host = $request->getHost();
        $schema = $request->getScheme();

        return sprintf('%s://%s/oauth', $schema, $host);
    }
    protected abstract function getAuthorizeUrl(): string;
    protected abstract function getTokenUrl(): string;
    protected abstract function getClientId(): string;
    protected abstract function getClientSecret(): string;
    public abstract function getIdentity(string $accessToken, string $tokenType): ?OAuthIdentity;
    protected abstract function getScope(OauthState $oauthState): string;

    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }
}
