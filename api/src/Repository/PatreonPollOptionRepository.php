<?php

namespace App\Repository;

use App\Entity\PatreonCampaignTier;
use App\Entity\Poll;
use App\Entity\PollOption;
use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;

class PatreonPollOptionRepository extends AbstractBaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PollOption::class);
    }

    public function getOptionsForPoll(Poll $poll): array
    {
        return $this->findBy([
            'poll' => $poll
        ]);
    }

    public function findMyOptions(Poll $poll, User $user): array
    {
        return $this->findBy([
            'poll' => $poll,
            'createdBy' => $user,
        ]);
    }
}
