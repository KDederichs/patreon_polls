<?php

namespace App\EventListener;

use App\Entity\SubscribestarUser;
use App\Event\PostOauthResourceConnectedEvent;
use App\Service\SubscribestarService;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener(event: PostOauthResourceConnectedEvent::class, method: 'onConnected')]
class SubscribestarConnectedSubscriber
{
    public function __construct(
        private readonly SubscribestarService $subscribestarService
    )
    {

    }

    public function onConnected(PostOauthResourceConnectedEvent $event): void
    {
        $oauthResource = $event->getOauthResource();
        if (!$oauthResource instanceof SubscribestarUser) {
            return;
        }

        if ($oauthResource->isCreator()) {
            $this->subscribestarService->refreshTiers($oauthResource);
        }
        $this->subscribestarService->getSubscriptions($oauthResource);
    }
}
