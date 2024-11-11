<?php

namespace App\Enum;

enum OAuthAuthType: string
{
    case Login = 'login';
    case Connect = 'connect';
    case ConnectAsCreator = 'creator_connect';
}
