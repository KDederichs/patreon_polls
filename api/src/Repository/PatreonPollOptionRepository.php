<?php

namespace App\Repository;

use App\Entity\PatreonCampaignTier;
use App\Entity\PatreonPoll;
use App\Entity\PatreonPollOption;
use Doctrine\Persistence\ManagerRegistry;

class PatreonPollOptionRepository extends AbstractBaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PatreonPollOption::class);
    }

    public function getOptionsForPoll(PatreonPoll $poll): array
    {
        return $this->findBy([
            'poll' => $poll
        ]);
    }
}
