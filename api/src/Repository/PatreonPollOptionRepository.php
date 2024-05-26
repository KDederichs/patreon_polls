<?php

namespace App\Repository;

use App\Entity\PatreonCampaignTier;
use App\Entity\PatreonPoll;
use App\Entity\PatreonPollOption;
use App\Entity\User;
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

    public function findMyOptions(PatreonPoll $poll, User $user): array
    {
        return $this->findBy([
            'poll' => $poll,
            'createdBy' => $user,
        ]);
    }
}
