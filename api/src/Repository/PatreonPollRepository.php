<?php

namespace App\Repository;

use App\Entity\PatreonCampaignTier;
use App\Entity\PatreonPoll;
use App\Entity\PatreonPollOption;
use App\Entity\PatreonPollVote;
use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;

class PatreonPollRepository extends AbstractBaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PatreonPoll::class);
    }
}
