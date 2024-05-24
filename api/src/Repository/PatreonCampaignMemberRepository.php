<?php

namespace App\Repository;

use App\Entity\PatreonCampaign;
use App\Entity\PatreonCampaignMember;
use Doctrine\Persistence\ManagerRegistry;

class PatreonCampaignMemberRepository   extends AbstractBaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PatreonCampaignMember::class);
    }

    public function findByCampaignAndPatreonUserId(PatreonCampaign $campaign, string $patreonUserId): ?PatreonCampaignMember
    {
        return $this->findOneBy([
            'campaign' => $campaign,
            'patreonUserId' => $patreonUserId
        ]);
    }

}
