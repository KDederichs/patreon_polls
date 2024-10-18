<?php

namespace App\Event;

use App\Entity\OauthResource;
use Symfony\Contracts\EventDispatcher\Event;

class OauthResourceConnectedEvent extends Event
{
    public function __construct(
        private readonly OauthResource $oauthResource
    )
    {

    }

    public function getOauthResource(): OauthResource
    {
        return $this->oauthResource;
    }
}
