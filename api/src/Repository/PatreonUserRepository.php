<?php

namespace App\Repository;

use App\Entity\OauthResource;
use App\Entity\PatreonCampaignWebhook;
use App\Entity\PatreonUser;
use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;

class PatreonUserRepository extends AbstractBaseRepository implements ResourceOwnedInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PatreonUser::class);
    }

    public function findByPatreonId(string $patreonId, bool $creator = false): ?PatreonUser
    {
        return $this->findOneBy([
            'resourceId' => $patreonId,
            'creator' => $creator
        ]);
    }

    public function findByUser(User $user): ?PatreonUser
    {
        return $this->findOneBy([
            'user' => $user,
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

    public function getOAuthResource(string $resourceId, bool $creator): ?OauthResource
    {
        return $this->findByPatreonId($resourceId, $creator);
    }
}
