<?php

namespace App\Controller;

use App\Entity\User;
use App\Message\RefreshSubscribestarSubscriptionsMessage;
use App\Repository\SubscribestarUserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class SubscribestarController extends AbstractController
{
    public function __construct(
        private readonly MessageBusInterface $bus,
        private readonly SubscribestarUserRepository $subscribestarUserRepository,
    )
    {

    }

    #[Route('/subscribestar/refresh', methods: ['POST'])]
    public function refreshSubscriptions(#[CurrentUser] ?User $user = null): Response
    {
        if (null === $user) {
            throw $this->createAccessDeniedException();
        }

        $subscribestarUser = $this->subscribestarUserRepository->findByUser($user);
        if (null === $subscribestarUser) {
            throw $this->createNotFoundException();
        }

        $this->bus->dispatch(new RefreshSubscribestarSubscriptionsMessage($subscribestarUser->getId()));

        return new Response(null, Response::HTTP_NO_CONTENT);
    }
}
