<?php

namespace App\Security\Oauth;

use App\Entity\User;
use App\Repository\UserRepository;
use Carbon\CarbonImmutable;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use HWI\Bundle\OAuthBundle\Security\Core\User\OAuthAwareUserProviderInterface;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Uid\Uuid;

class PatreonUserProvider implements OAuthAwareUserProviderInterface, UserProviderInterface
{
    public function __construct(
        private readonly UserRepository $userRepository
    )
    {

    }

    public function loadUserByOAuthUserResponse(UserResponseInterface $response): UserInterface
    {
        $userData = $response->getData()['data']['attributes'];
        $patreonIdentifier = $response->getData()['data']['id'];
        $user = $this->userRepository->findByPatreonId($patreonIdentifier);
        if (!$user) {
            $user = new User();
            $userName = $userData['full_name'] ?? 'Unknown User';
            $user
                ->setPatreonId($patreonIdentifier)
                ->setPatreonAccessToken($response->getAccessToken())
                ->setPatreonRefreshToken($response->getRefreshToken())
                ->setPatreonTokenType($response->getOAuthToken()->getRawToken()['token_type'] ?? 'Bearer')
                ->setPatreonScope($response->getOAuthToken()->getRawToken()['scope'] ?? null)
                ->setPatreonTokenExpiresAt(CarbonImmutable::now()->addSeconds($response->getExpiresIn() ?? 3600))
                ->setUsername($userName)
            ;
            $this->userRepository->persist($user);
            $this->userRepository->save();
        }
        return $user;
    }

    public function refreshUser(UserInterface $user): UserInterface
    {
        $identifier = $user->getUserIdentifier();
        return $this->loadUserByIdentifier($identifier);
    }

    public function supportsClass(string $class): bool
    {
        return $class === User::class;
    }

    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        if (!Uuid::isValid($identifier)) {
            throw new \InvalidArgumentException('Invalid user identifier.');
        }

        $user = $this->userRepository->find(Uuid::fromString($identifier));

        if (!$user) {
            throw new UserNotFoundException();
        }
        return $user;
    }
}
