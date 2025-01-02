<?php

namespace App\Repository;

use App\Entity\OauthResource;
use App\Entity\PatreonCampaignWebhook;
use App\Entity\PatreonUser;
use App\Entity\SubscribestarUser;
use App\Entity\User;
use Carbon\CarbonImmutable;
use Doctrine\Persistence\ManagerRegistry;

class SubscribestarUserRepository extends AbstractBaseRepository implements ResourceOwnedInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SubscribestarUser::class);
    }

    public function findBySubscribestarId(string $patreonId, bool $creator = false): ?SubscribestarUser
    {
        return $this->findOneBy([
            'resourceId' => $patreonId,
            'creator' => $creator
        ]);
    }

    public function findByUser(User $user): ?SubscribestarUser
    {
        return $this->findOneBy([
            'user' => $user,
        ]);
    }

    public function findForTokenRenew(): array
    {
        $qb = $this->createQueryBuilder('su');
        return $qb
            ->select('su')
            ->where(':date >= su.accessTokenExpiresAt')
            ->setParameter('date',CarbonImmutable::now()->addDays(2)->toDateTimeImmutable())
            ->getQuery()
            ->getResult();
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
        return $this->findBySubscribestarId($resourceId, $creator);
    }
}
