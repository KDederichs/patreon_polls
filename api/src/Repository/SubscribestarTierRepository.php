<?php

namespace App\Repository;

use App\Entity\PatreonCampaign;
use App\Entity\PatreonCampaignTier;
use App\Entity\SubscribestarTier;
use App\Entity\SubscribestarUser;
use Doctrine\Persistence\ManagerRegistry;

class SubscribestarTierRepository   extends AbstractBaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SubscribestarTier::class);
    }

    public function findBySubscribestarTierId(string $subscribestarTierId): ?SubscribestarTier
    {
        return $this->findOneBy(['subscribestarTierId' => $subscribestarTierId]);
    }

    public function findBySubscribestarUser(SubscribestarUser $user): array
    {
        $this->findBy([
            'subscribestarUser' => $user
        ]);
    }
}
