<?php

namespace App\Controller;

use ApiPlatform\Metadata\IriConverterInterface;
use App\Dto\OAuth\OAuthConnectPayload;
use App\Entity\OauthResource;
use App\Entity\OauthState;
use App\Entity\User;
use App\Enum\OAuthAuthType;
use App\Event\PostOauthResourceConnectedEvent;
use App\Event\PreOauthResourceConnectedEvent;
use App\Repository\ApiTokenRepository;
use App\Repository\OAuthStateRepository;
use App\Repository\PatreonUserRepository;
use App\Repository\ResourceOwnedInterface;
use App\Repository\SubscribestarUserRepository;
use App\Repository\UserRepository;
use App\Service\Oauth\GenericOAuthService;
use App\Service\Oauth\PatreonOAuthService;
use App\Service\Oauth\SubscribestarOAuthService;
use Carbon\CarbonImmutable;
use Psr\Container\ContainerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Uid\Uuid;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\Service\ServiceSubscriberInterface;

class OAuthController extends AbstractController implements ServiceSubscriberInterface
{

    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly EventDispatcherInterface $dispatcher,
        private readonly ContainerInterface $locator,
        private readonly Security $security,
        private readonly OAuthStateRepository $authStateRepository,
        private readonly ApiTokenRepository $apiTokenRepository,
        private readonly IriConverterInterface $iriConverter,
    )
    {

    }

    public static function getSubscribedServices(): array
    {
        return [
            'patreon_repo' => PatreonUserRepository::class,
            'patreon_oauth' => PatreonOAuthService::class,
            'subscribestar_oauth' => SubscribestarOAuthService::class,
            'subscribestar_repo' => SubscribestarUserRepository::class,
        ];
    }


    #[Route('/oauth/connect', methods: ['POST'])]
    public function connectOauth(
        Request $request,
        #[MapRequestPayload] OAuthConnectPayload $payload
    )
    {
        $code = $payload->getCode();
        $stateId = $payload->getState();
        /** @var OauthState|null $oAuthState */
        $oAuthState = $this->authStateRepository->find(Uuid::fromString($stateId));
        if (!$oAuthState) {
            throw new BadRequestHttpException('Invalid Auth state');
        }
        $provider = $oAuthState->getProvider();
        $resourceOwnerName = $provider->value;
        /** @var ResourceOwnedInterface $resourceRepository */
        $resourceRepository = $this->locator->get(sprintf('%s_repo', $resourceOwnerName));
        /** @var GenericOAuthService $oauth */
        $oauth = $this->locator->get(sprintf('%s_oauth', $resourceOwnerName));

        $oauthToken = $oauth->getAccessToken($code);

        if (!$oauthToken) {
            throw new BadRequestHttpException('Invalid OAuth Token');
        }

        $identity = $oauth->getIdentity($oauthToken->getAccessToken(), $oauthToken->getTokenType());

        $user = $oAuthState->getUser() ?? $this->security->getUser();
        $user = $user ?? $this->userRepository->findByResourceOwnerId($resourceOwnerName, $identity->getId());
        if (!$user) {
            $user = new User();
            $this->userRepository->persist($user);
        }

        if (!$user->{'get'.ucfirst($resourceOwnerName).'Id'}()) {
            $user->{'set'.ucfirst($resourceOwnerName).'Id'}($identity->getId());
        }

        $oauthResource = $resourceRepository->getOAuthResource($identity->getId());
        if (!$oauthResource) {
            $clazz = 'App\\Entity\\'.ucfirst($resourceOwnerName).'User';
            /** @var OauthResource $oauthResource */
            $oauthResource = new $clazz();
            $oauthResource
                ->setUser($user)
                ->setResourceId($identity->getId());
        }

        if (!$user->getUsername()) {
            $user->setUsername($identity->getUsername());
        }


        $oauthResource
            ->setAccessToken($oauthToken->getAccessToken())
            ->setRefreshToken($oauthToken->getRefreshToken())
            ->setTokenType($oauthToken->getTokenType())
            ->setScope($oauthToken->getScope())
            ->setAccessTokenExpiresAt(CarbonImmutable::now()->addSeconds($oauthToken->getExpiresIn()))
            ->setUsername($identity->getUsername())
            ->setCreator($oAuthState->getAuthType() === OAuthAuthType::ConnectAsCreator);

        $this->dispatcher->dispatch(new PreOauthResourceConnectedEvent($oauthResource));

        $this->userRepository->persist($oauthResource);
        $this->authStateRepository->remove($oAuthState);
        $this->userRepository->save();

        $this->dispatcher->dispatch(new PostOauthResourceConnectedEvent($oauthResource));

        $token = $request->headers->has('Authorization') ? explode(' ',$request->headers->get('Authorization'))[1] : null;
        if (!$token) {
            $apiToken = $this->apiTokenRepository->createForUser($user);
            $token = sprintf('%s.%s', $apiToken->getId()->toBase58(), $apiToken->getTokenPlain());
        }

        return new JsonResponse([
            'token' => $token,
            'userIri' => $this->iriConverter->getIriFromResource($user),
            'mode' => $oAuthState->getAuthType() === OAuthAuthType::Login ? 'login' : 'connect',
        ]);
    }
}
