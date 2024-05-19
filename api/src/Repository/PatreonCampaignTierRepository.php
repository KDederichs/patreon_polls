<?php

namespace App\Repository;

use App\Entity\PatreonCampaign;
use App\Entity\PatreonCampaignTier;
use Doctrine\Persistence\ManagerRegistry;

class PatreonCampaignTierRepository   extends AbstractBaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PatreonCampaignTier::class);
    }

    public function findByPatreonTierId(string $patreonTierId): ?PatreonCampaignTier
    {
        return $this->findOneBy(['patreonTierId' => $patreonTierId]);
    }

    public function findForCampaign(PatreonCampaign $campaign): array
    {
        $this->findBy([
            'campaign' => $campaign
        ]);
    }
}
