<?php

namespace App\Repository;

use App\Entity\PatreonCampaign;
use Doctrine\Persistence\ManagerRegistry;

class PatreonCampaignRepository   extends AbstractBaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PatreonCampaign::class);
    }

    public function findByPatreonCampaignId(string $patreonCampaignId): ?PatreonCampaign
    {
        return $this->findOneBy(['patreonCampaignId' => $patreonCampaignId]);
    }

}
