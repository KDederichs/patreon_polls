<?php

namespace App\Security\Oauth;

use App\Entity\OauthResource;
use App\Entity\User;
use App\Event\PostOauthResourceConnectedEvent;
use App\Event\PreOauthResourceConnectedEvent;
use App\Repository\PatreonUserRepository;
use App\Repository\ResourceOwnedInterface;
use App\Repository\UserRepository;
use App\Util\SentryHelper;
use Carbon\CarbonImmutable;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use HWI\Bundle\OAuthBundle\Security\Core\User\OAuthAwareUserProviderInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\Service\ServiceSubscriberInterface;
use Psr\Container\ContainerInterface;

class OauthUserProvider implements OAuthAwareUserProviderInterface, ServiceSubscriberInterface
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly EventDispatcherInterface $dispatcher,
        private readonly ContainerInterface $locator,
        private readonly Security $security
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
        $isCreatorResource = str_contains($resourceOwner->getName(), '_creator');

        $userData = $response->getData()['data']['attributes'];
        $resourceIdentifier = $response->getData()['data']['id'];

        $user = $this->security->getUser() ?? $this->userRepository->findByResourceOwnerId($resourceOwnerName, $resourceIdentifier);
        if (!$user) {
            $user = new User();
            $this->userRepository->persist($user);
        }

        if (!$user->{'get'.ucfirst($resourceOwnerName).'Id'}) {
            $user->{'set'.ucfirst($resourceOwnerName).'Id'}($resourceIdentifier);
        }

        /** @var ResourceOwnedInterface $resourceRepository */
        $resourceRepository = $this->locator->get($resourceOwnerName);

        $oauthResource = $resourceRepository->getOAuthResource($resourceIdentifier, $isCreatorResource);

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
            ->setUsername($userName)
            ->setCreator($response->getResourceOwner()->getName() === 'patreon_creator');

        $this->dispatcher->dispatch(new PreOauthResourceConnectedEvent($oauthResource));

        $this->userRepository->persist($oauthResource);
        $this->userRepository->save();

        $this->dispatcher->dispatch(new PostOauthResourceConnectedEvent($oauthResource));

        return $user;
    }

    public static function getSubscribedServices(): array
    {
        return [
            'patreon' => PatreonUserRepository::class,
        ];
    }
}
