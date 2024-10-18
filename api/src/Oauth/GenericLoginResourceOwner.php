<?php

namespace App\Oauth;

use HWI\Bundle\OAuthBundle\OAuth\ResourceOwner\GenericOAuth2ResourceOwner;

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
            'redirect_uri' => substr($redirectUri,0, strrpos($redirectUri, '/oauth')).'/login/check?provider='.$this->getName(),
        ], $extraParameters);

        return $this->normalizeUrl($this->options['authorization_url'], $parameters);
    }
}
