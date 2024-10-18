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
    public function findByResourceOwnerId(string $resourceOwner, string $id): ?User
    {
        return $this->findOneBy([lcfirst($resourceOwner).'Id' => $id]);
    }
}
