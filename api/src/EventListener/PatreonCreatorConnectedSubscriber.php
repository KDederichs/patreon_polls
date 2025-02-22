<?php

namespace App\EventListener;

use App\Entity\PatreonUser;
use App\Event\PostOauthResourceConnectedEvent;
use App\Event\PreOauthResourceConnectedEvent;
use App\Service\PatreonService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;

class PatreonCreatorConnectedSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly PatreonService $patreonService
    )
    {

    }


    public function onPreConnect(PreOauthResourceConnectedEvent $event): void
    {
        $oauthResource = $event->getOauthResource();

        if (!$oauthResource instanceof PatreonUser || !$oauthResource->isCreator()) {
            return;
        }

        $user = $oauthResource->getUser();

        if ($user->getPatreonId() !== $oauthResource->getResourceId()) {
            throw new BadCredentialsException('The creator account you are trying to connect is not the same one you are logged in to.');
        }
    }


    public function onPostConnect(PostOauthResourceConnectedEvent $event): void
    {
        $oauthResource = $event->getOauthResource();

        if (!$oauthResource instanceof PatreonUser || !$oauthResource->isCreator()) {
            return;
        }

        $this->patreonService->syncPatreon($oauthResource);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            PostOauthResourceConnectedEvent::class => 'onPostConnect',
            PreOauthResourceConnectedEvent::class => 'onPreConnect',
        ];
    }
}
