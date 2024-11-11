<?php

namespace App\Enum;

enum OAuthProvider: string
{
    case Patreon = 'patreon';
    case SubscribeStar = 'subscribestar';
}
