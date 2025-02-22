<?php

namespace App\Repository;

use App\Entity\PatreonCampaignTier;
use App\Entity\Poll;
use App\Entity\PollOption;
use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;

class PollOptionRepository extends AbstractBaseRepository
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

    public function getNumberOfMyOptions(Poll $poll, User $user): int
    {
        $qb = $this->createQueryBuilder('po');
        return $qb
            ->select('count(po.id)')
            ->where('po.poll = :poll')
            ->andWhere('po.createdBy = :user')
            ->setParameter('poll', $poll)
            ->setParameter('user', $user)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function findMyOptions(Poll $poll, User $user): array
    {
        return $this->findBy([
            'poll' => $poll,
            'createdBy' => $user,
        ]);
    }
}
