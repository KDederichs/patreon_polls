<?php

namespace App\Security\Oauth;

use App\Entity\PatreonUser;
use App\Entity\User;
use App\Repository\PatreonCampaignMemberRepository;
use App\Repository\PatreonUserRepository;
use App\Repository\UserRepository;
use App\Util\SentryHelper;
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
        private readonly UserRepository $userRepository,
        private readonly PatreonUserRepository $patreonUserRepository,
        private readonly PatreonCampaignMemberRepository $campaignMemberRepository,
    )
    {

    }

    public function loadUserByOAuthUserResponse(UserResponseInterface $response): UserInterface
    {
        SentryHelper::addContext('oauthResponse', [
            'responseData' => $response->getData(),
        ]);
        $userData = $response->getData()['data']['attributes'];
        $patreonIdentifier = $response->getData()['data']['id'];
        $user = $this->userRepository->findByPatreonId($patreonIdentifier);
        if (!$user) {
            $user = new User();
            $user->setPatreonId($patreonIdentifier);
            $this->userRepository->persist($user);
        }

        $patreonUser = $this->patreonUserRepository->findByPatreonId($patreonIdentifier);

        if (!$patreonUser) {
            $patreonUser = new PatreonUser();
            $patreonUser
                ->setUser($user)
                ->setPatreonId($patreonIdentifier);
        }

        $userName = $userData['full_name'] ?? 'Unknown User';
        $patreonUser
            ->setPatreonAccessToken($response->getAccessToken())
            ->setPatreonRefreshToken($response->getRefreshToken())
            ->setPatreonTokenType($response->getOAuthToken()->getRawToken()['token_type'] ?? 'Bearer')
            ->setPatreonScope($response->getOAuthToken()->getRawToken()['scope'] ?? null)
            ->setPatreonTokenExpiresAt(CarbonImmutable::now()->addSeconds($response->getExpiresIn() ?? 3600))
            ->setCreator($response->getResourceOwner()->getName() === 'patreon_creator')
            ->setUsername($userName);
        ;

        foreach ($this->campaignMemberRepository->findUnconnectedMembershipsForId($patreonUser->getPatreonId()) as $campaignMember) {
            $campaignMember->setPatreonUser($patreonUser);
        }

        $this->userRepository->persist($patreonUser);
        $this->userRepository->save();
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
