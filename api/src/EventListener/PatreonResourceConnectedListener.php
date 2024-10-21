<?php

namespace App\EventListener;

use App\Entity\PatreonUser;
use App\Event\PreOauthResourceConnectedEvent;
use App\Repository\PatreonCampaignMemberRepository;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener(event: PreOauthResourceConnectedEvent::class, method: 'onConnected')]
class PatreonResourceConnectedListener
{
    public function __construct(
        private readonly PatreonCampaignMemberRepository $campaignMemberRepository,
    )
    {

    }

    public function onConnected(PreOauthResourceConnectedEvent $event): void
    {
        $oauthResource = $event->getOauthResource();

        if (!$oauthResource instanceof PatreonUser || $oauthResource->isCreator()) {
            return;
        }

        foreach ($this->campaignMemberRepository->findUnconnectedMembershipsForId($oauthResource->getResourceId()) as $campaignMember) {
            $campaignMember->setPatreonUser($oauthResource);
        }
    }
}
