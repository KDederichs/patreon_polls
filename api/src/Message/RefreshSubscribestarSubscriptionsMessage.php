<?php

namespace App\Message;

use Symfony\Component\Uid\Uuid;

readonly final class RefreshSubscribestarSubscriptionsMessage implements AsyncMessageInterface
{
    public function __construct(
       public Uuid $subscribestarUserId,
    ) {}
}
