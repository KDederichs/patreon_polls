<?php

namespace App\Repository;

use App\Entity\PatreonCampaignWebhook;
use App\Entity\PatreonUser;
use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;

class PatreonUserRepository extends AbstractBaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PatreonUser::class);
    }

    public function findByPatreonId(string $patreonId): ?PatreonUser
    {
        return $this->findOneBy([
            'resourceId' => $patreonId
        ]);
    }

    public function userIsCreator(User $user): bool
    {
        $qb = $this->createQueryBuilder('pu');
        return $qb
            ->select('count(pu.id)')
            ->where('pu.creator = :truthy')
            ->andWhere('pu.user = :user')
            ->setParameter('truthy', true)
            ->setParameter('user', $user)
            ->getQuery()
            ->getSingleScalarResult() > 0;
    }
}
