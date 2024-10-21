<?php

namespace App\Oauth;

class GenericCreatorResourceOwner extends GenericFrontendResourceOwner
{

    protected function substituteRedirectUri(string $redirectUri): string
    {
        return substr($redirectUri,0, strrpos($redirectUri, '/creator/oauth')).'/user/settings?provider='.$this->getName();
    }
}
