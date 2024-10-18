<?php

namespace App\EventListener;

use App\Entity\PatreonUser;
use App\Event\OauthResourceConnectedEvent;
use App\Repository\PatreonCampaignMemberRepository;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener(event: OauthResourceConnectedEvent::class, method: 'onConnected')]
class PatreonResourceConnectedListener
{
    public function __construct(
        private readonly PatreonCampaignMemberRepository $campaignMemberRepository,
    )
    {

    }

    public function onConnected(OauthResourceConnectedEvent $event): void
    {
        $oauthResource = $event->getOauthResource();

        if (!$oauthResource instanceof PatreonUser) {
            return;
        }

        foreach ($this->campaignMemberRepository->findUnconnectedMembershipsForId($oauthResource->getResourceId()) as $campaignMember) {
            $campaignMember->setPatreonUser($oauthResource);
        }
    }
}
