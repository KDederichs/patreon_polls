<?php

namespace App\Controller;

use App\Entity\PatreonPoll;
use App\Entity\PatreonPollOption;
use App\Entity\User;
use App\Repository\PatreonPollOptionRepository;
use App\Repository\PatreonPollVoteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class PollController extends AbstractController
{

    public function __construct(
        private readonly PatreonPollOptionRepository $patreonPollOptionRepository,
        private readonly PatreonPollVoteRepository $patreonPollVoteRepository
    )
    {

    }

    #[Route('/', name: 'poll_index')]
    public function index(): Response
    {
        return $this->render('index.html.twig');
    }

    #[Route('/poll/{poll}/vote', name: 'poll_vote')]
    public function votePoll(PatreonPoll $poll, #[CurrentUser] User $user): Response
    {
        $options = $this->patreonPollOptionRepository->getOptionsForPoll($poll);
        $myVotes = $this->patreonPollVoteRepository->findMyVotes($user, $poll);

        return $this->render('vote_poll.html.twig', [
            'poll' => $poll,
            'pollOptions' => $options,
            'myVotes' => $myVotes,
        ]);
    }

    #[Route('/poll/create', name: 'poll_create')]
    public function createPoll(): Response
    {
        return $this->render('create_poll.html.twig');
    }


}
