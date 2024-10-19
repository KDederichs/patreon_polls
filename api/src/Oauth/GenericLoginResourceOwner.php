<?php

namespace App\Oauth;

use HWI\Bundle\OAuthBundle\OAuth\ResourceOwner\GenericOAuth2ResourceOwner;
use HWI\Bundle\OAuthBundle\Security\OAuthErrorHandler;
use Symfony\Component\HttpFoundation\Request as HttpRequest;

class GenericLoginResourceOwner extends GenericOAuth2ResourceOwner
{
    public const TYPE = 'oauth2';

    public function getAuthorizationUrl($redirectUri, array $extraParameters = [])
    {
        $parameters = array_merge([
            'response_type' => 'code',
            'client_id' => $this->options['client_id'],
            'scope' => $this->options['scope'],
            'state' => $this->state->encode(),
            'redirect_uri' => $this->substituteRedirectUri($redirectUri),
        ], $extraParameters);

        return $this->normalizeUrl($this->options['authorization_url'], $parameters);
    }

    public function getAccessToken(HttpRequest $request, $redirectUri, array $extraParameters = [])
    {
        OAuthErrorHandler::handleOAuthError($request);

        $parameters = array_merge([
            'code' => $request->query->get('code'),
            'grant_type' => 'authorization_code',
            'redirect_uri' => $this->substituteRedirectUri($redirectUri),
        ], $extraParameters);

        $response = $this->doGetTokenRequest($this->options['access_token_url'], $parameters);
        $response = $this->getResponseContent($response);

        $this->validateResponseContent($response);

        return $response;
    }

    private function substituteRedirectUri(string $redirectUri): string
    {
        return substr($redirectUri,0, strrpos($redirectUri, '/oauth')).'/login/check?provider='.$this->getName();
    }
}
