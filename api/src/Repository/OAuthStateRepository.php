<?php

namespace App\Repository;


use App\Entity\OauthState;
use Doctrine\Persistence\ManagerRegistry;

class OAuthStateRepository extends AbstractBaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OauthState::class);
    }
}
