<?php

namespace App\Repository;

use App\Entity\ApiToken;
use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;

class ApiTokenRepository extends AbstractBaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ApiToken::class);
    }

    public function findByTokenString(#[\SensitiveParameter] string $accessToken): ?ApiToken
    {
        return $this->findOneBy(['token' => $accessToken]);
    }

    public function createForUser(User $user): ApiToken
    {
        $token = new ApiToken();
        $token->setOwnedBy($user);
        $this->persist($token);
        $this->save();

        return $token;
    }

    public function deleteToken(string $token): void
    {
        $tokenObj = $this->findOneBy([
            'token' => $token
        ]);
        if ($tokenObj) {
            $this->remove($tokenObj);
            $this->save();
        }
    }
}
