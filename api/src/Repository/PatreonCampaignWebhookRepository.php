<?php

namespace App\Repository;

use App\Entity\PatreonCampaign;
use App\Entity\PatreonCampaignWebhook;
use Doctrine\Persistence\ManagerRegistry;

class PatreonCampaignWebhookRepository extends AbstractBaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PatreonCampaignWebhook::class);
    }

    public function findByCampaign(PatreonCampaign $campaign): ?PatreonCampaignWebhook
    {
        return $this->findOneBy([
            'campaign' => $campaign
        ]);
    }
}
