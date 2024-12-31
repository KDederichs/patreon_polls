<?php

namespace App\Repository;

use App\Entity\ApiToken;
use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Uid\Uuid;

class ApiTokenRepository extends AbstractBaseRepository
{
    public function __construct(
        ManagerRegistry $registry,
        private  readonly UserPasswordHasherInterface $passwordHasher)
    {
        parent::__construct($registry, ApiToken::class);
    }

    public function findByTokenString(#[\SensitiveParameter] string $accessToken): ?ApiToken
    {
        return $this->findOneBy(['token' => $accessToken]);
    }

    public function findByLocator(Uuid $tokenId): ?ApiToken
    {
        return $this->find($tokenId);
    }

    public function createForUser(User $user): ApiToken
    {
        $token = new ApiToken();
        $token->setOwnedBy($user);
        $token->setToken($this->passwordHasher->hashPassword($user, $token->getTokenPlain()));
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
