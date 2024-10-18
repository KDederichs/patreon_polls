<?php

namespace App\Security\Oauth;

use App\Entity\OauthResource;
use App\Entity\User;
use App\Event\OauthResourceConnectedEvent;
use App\Repository\PatreonUserRepository;
use App\Repository\UserRepository;
use App\Util\SentryHelper;
use Carbon\CarbonImmutable;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use HWI\Bundle\OAuthBundle\Security\Core\User\OAuthAwareUserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class OauthUserProvider implements OAuthAwareUserProviderInterface
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly PatreonUserRepository $patreonUserRepository,
        private readonly EventDispatcherInterface $dispatcher,
    )
    {

    }

    public function loadUserByOAuthUserResponse(UserResponseInterface $response): UserInterface
    {
        SentryHelper::addContext('oauthResponse', [
            'responseData' => $response->getData(),
        ]);

        $resourceOwner = $response->getResourceOwner();
        $resourceOwnerName = str_replace('_creator','',$resourceOwner->getName());

        $userData = $response->getData()['data']['attributes'];
        $resourceIdentifier = $response->getData()['data']['id'];
        $user = $this->userRepository->findByResourceOwnerId($resourceOwnerName, $resourceIdentifier);
        if (!$user) {
            $user = new User();
            $user->{'set'.ucfirst($resourceOwnerName).'Id'}($resourceIdentifier);
            $this->userRepository->persist($user);
        }

        $oauthResource = $this->patreonUserRepository->findByPatreonId($resourceIdentifier);

        if (!$oauthResource) {
            $clazz = 'App\\Entity\\'.ucfirst($resourceOwnerName).'User';
            /** @var OauthResource $oauthResource */
            $oauthResource = new $clazz();
            $oauthResource
                ->setUser($user)
                ->setResourceId($resourceIdentifier);
        }

        $userName = $userData['full_name'] ?? 'Unknown User';
        if (!$user->getUsername()) {
            $user->setUsername($userName);
        }

        $oauthResource
            ->setAccessToken($response->getAccessToken())
            ->setRefreshToken($response->getRefreshToken())
            ->setTokenType($response->getOAuthToken()->getRawToken()['token_type'] ?? 'Bearer')
            ->setScope($response->getOAuthToken()->getRawToken()['scope'] ?? null)
            ->setAccessTokenExpiresAt(CarbonImmutable::now()->addMonths())
            ->setCreator($response->getResourceOwner()->getName() === 'patreon_creator');

        $this->dispatcher->dispatch(new OauthResourceConnectedEvent($oauthResource));

        $this->userRepository->persist($oauthResource);
        $this->userRepository->save();
        return $user;
    }
}
