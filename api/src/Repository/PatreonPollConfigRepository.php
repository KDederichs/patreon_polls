<?php

namespace App\Repository;

use App\Entity\PatreonCampaign;
use App\Entity\PatreonCampaignMember;
use App\Entity\PatreonCampaignTier;
use App\Entity\Poll;
use App\Entity\PatreonPollVoteConfig;
use Doctrine\Persistence\ManagerRegistry;

class PatreonPollConfigRepository   extends AbstractBaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PatreonPollVoteConfig::class);
    }

    public function findByCampaignTierAndPoll(PatreonCampaignTier $campaignTier, Poll $patreonPoll): ?PatreonPollVoteConfig
    {
        return $this->findOneBy([
            'campaignTier' => $campaignTier,
            'poll' => $patreonPoll,
        ]);
    }

    /**
     * @return array<PatreonPollVoteConfig>
     */
    public function findByPoll(Poll $poll): array
    {
        return $this->findBy([
            'poll' => $poll,
        ]);
    }
}
