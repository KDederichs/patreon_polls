<?php

namespace App\Controller;

use App\Entity\PatreonPoll;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class PollController extends AbstractController
{
    #[Route('/', name: 'poll_index')]
    public function index(): Response
    {
        return $this->render('index.html.twig');
    }

    #[Route('/poll/{poll}/vote', name: 'poll_vote')]
    public function votePoll(PatreonPoll $poll): Response
    {
        return $this->render('vote_poll.html.twig', [
            'poll' => $poll,
        ]);
    }

    #[Route('/poll/create', name: 'poll_create')]
    public function createPoll(): Response
    {
        return $this->render('create_poll.html.twig');
    }


}
