<?php

namespace App\Repository;

use App\Entity\PatreonCampaignTier;
use App\Entity\Poll;
use App\Entity\PollOption;
use App\Entity\PollVote;
use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;

class PatreonPollRepository extends AbstractBaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Poll::class);
    }
}
