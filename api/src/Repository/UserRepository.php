<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;

class UserRepository  extends AbstractBaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }
    public function findByPatreonId(string $patreonId): ?User
    {
        return $this->findOneBy(['patreonId' => $patreonId]);
    }
}
