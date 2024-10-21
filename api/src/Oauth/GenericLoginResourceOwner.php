<?php

namespace App\Oauth;

class GenericLoginResourceOwner extends GenericFrontendResourceOwner
{

    protected function substituteRedirectUri(string $redirectUri): string
    {
        return substr($redirectUri,0, strrpos($redirectUri, '/oauth')).'/login/check?provider='.$this->getName();
    }
}
