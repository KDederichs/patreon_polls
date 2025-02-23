<?php

namespace App\Repository;

use App\Entity\PatreonCampaignTier;
use App\Entity\Poll;
use App\Entity\PollOption;
use App\Entity\PollVote;
use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;

class PollVoteRepository extends AbstractBaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PollVote::class);
    }

    public function findMyVotes(User $user, Poll $poll): array
    {
        return $this->findBy([
            'poll' => $poll,
            'votedBy' => $user
        ]);
    }

    public function hasVotedForOption(User $user, PollOption $option): bool
    {
        return $this->findOneBy([
            'option' => $option,
            'votedBy' => $user,
        ]) !== null;
    }

    public function getNumberOfVotesForPoll(User $user, Poll $poll): int
    {
        $qb = $this->createQueryBuilder('pv');
        return $qb
            ->select('count(pv.id)')
            ->where('pv.poll = :poll')
            ->andWhere('pv.votedBy = :user')
            ->setParameter('poll', $poll)
            ->setParameter('user', $user)
            ->getQuery()
            ->getSingleScalarResult();
    }
}
