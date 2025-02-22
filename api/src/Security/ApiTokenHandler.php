<?php

namespace App\Security;

use App\Repository\ApiTokenRepository;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Http\AccessToken\AccessTokenHandlerInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Uid\UuidV7;

class ApiTokenHandler implements AccessTokenHandlerInterface
{

    public function __construct(
        private readonly ApiTokenRepository $apiTokenRepository,
        private readonly PasswordHasherFactoryInterface $hasherFactory,
    )
    {

    }

    public function getUserBadgeFrom(#[\SensitiveParameter] string $accessToken): UserBadge
    {
        list($tokenLocator, $plainToken) = explode('.',$accessToken);


        if (!UuidV7::isValid($tokenLocator, Uuid::FORMAT_BASE_58)) {
            throw new BadCredentialsException();
        }

        $token = $this->apiTokenRepository->findByLocator(UuidV7::fromBase58($tokenLocator));

        if (!$token) {
            throw new BadCredentialsException();
        }

        $hasher = $this->hasherFactory->getPasswordHasher($token->getOwnedBy());

        if (!$hasher->verify($token->getToken(), $plainToken)) {
            throw new BadCredentialsException();
        }

        return new UserBadge($token->getOwnedBy()->getUserIdentifier());
    }
}
