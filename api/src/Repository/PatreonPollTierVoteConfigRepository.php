<?php

namespace App\Repository;

use App\Entity\PatreonCampaign;
use App\Entity\PatreonCampaignMember;
use App\Entity\PatreonCampaignTier;
use App\Entity\PatreonPoll;
use App\Entity\PatreonPollTierVoteConfig;
use Doctrine\Persistence\ManagerRegistry;

class PatreonPollTierVoteConfigRepository   extends AbstractBaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PatreonPollTierVoteConfig::class);
    }

    public function findByCampaignTierAndPoll(PatreonCampaignTier $campaignTier, PatreonPoll $patreonPoll): ?PatreonPollTierVoteConfig
    {
        return $this->findOneBy([
            'campaignTier' => $campaignTier,
            'patreonPoll' => $patreonPoll,
        ]);
    }

}
