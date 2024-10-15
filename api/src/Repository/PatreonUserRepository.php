<?php

namespace App\Repository;

use App\Entity\PatreonCampaignWebhook;
use App\Entity\PatreonUser;
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
            'patreonId' => $patreonId
        ]);
    }
}
