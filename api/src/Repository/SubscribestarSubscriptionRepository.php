<?php

namespace App\Repository;

use App\Entity\PatreonCampaign;
use App\Entity\PatreonCampaignTier;
use App\Entity\SubscribestarSubscription;
use App\Entity\SubscribestarTier;
use App\Entity\SubscribestarUser;
use Doctrine\Persistence\ManagerRegistry;

class SubscribestarSubscriptionRepository   extends AbstractBaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SubscribestarSubscription::class);
    }

    public function findBySubscribestarId(string $subscribestarId): ?SubscribestarSubscription
    {
        return $this->findOneBy(['subscribestarId' => $subscribestarId]);
    }

    /**
     * @param string $tierId
     * @return array<SubscribestarSubscription>
     */
    public function findForTierId(string $tierId): array
    {
        return $this->findBy([
            'tierId' => $tierId
        ]);
    }
}
