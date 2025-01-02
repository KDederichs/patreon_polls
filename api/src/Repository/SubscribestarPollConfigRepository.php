<?php

namespace App\Repository;

use App\Entity\PatreonCampaign;
use App\Entity\PatreonCampaignMember;
use App\Entity\PatreonCampaignTier;
use App\Entity\Poll;
use App\Entity\PatreonPollVoteConfig;
use App\Entity\SubscribestarPollVoteConfig;
use App\Entity\SubscribestarTier;
use Doctrine\Persistence\ManagerRegistry;

class SubscribestarPollConfigRepository   extends AbstractBaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SubscribestarPollVoteConfig::class);
    }

    public function findByCampaignTierAndPoll(SubscribestarTier $campaignTier, Poll $patreonPoll): ?SubscribestarPollVoteConfig
    {
        return $this->findOneBy([
            'campaignTier' => $campaignTier,
            'poll' => $patreonPoll,
        ]);
    }

    /**
     * @return array<SubscribestarPollVoteConfig>
     */
    public function findByPoll(Poll $poll): array
    {
        return $this->findBy([
            'poll' => $poll,
        ]);
    }
}
