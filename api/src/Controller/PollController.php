<?php

namespace App\Controller;

use App\Entity\PatreonPoll;
use App\Entity\PatreonPollOption;
use App\Entity\User;
use App\Repository\PatreonCampaignMemberRepository;
use App\Repository\PatreonPollOptionRepository;
use App\Repository\PatreonPollTierVoteConfigRepository;
use App\Repository\PatreonPollVoteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class PollController extends AbstractController
{

    public function __construct(
        private readonly PatreonPollOptionRepository $patreonPollOptionRepository,
        private readonly PatreonPollVoteRepository $patreonPollVoteRepository,
        private readonly PatreonPollTierVoteConfigRepository $configRepository,
        private readonly PatreonCampaignMemberRepository $campaignMemberRepository,
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
        $member = $this->campaignMemberRepository->findByCampaignAndPatreonUserId($poll->getCampaign(), $user->getPatreonId());
        $tier = $member?->getHighestEntitledTier()?->getTier();
        $voteConfig = null;
        if ($tier) {
            $voteConfig = $this->configRepository->findByCampaignTierAndPoll($tier, $poll);
        }
        $myOptions = $this->patreonPollOptionRepository->findMyOptions($poll, $user);

        return $this->render('vote_poll.html.twig', [
            'poll' => $poll,
            'pollOptions' => $options,
            'myVotes' => $myVotes,
            'tier' => $tier,
            'voteConfig' => $voteConfig,
            'myOptionCount' => count($myOptions),
            'hasEnded' => $poll->getEndsAt() !== null && $poll->getEndsAt()->isPast()
        ]);
    }

    #[Route('/poll/create', name: 'poll_create')]
    public function createPoll(#[CurrentUser] User $user): Response
    {
        return $this->render('create_poll.html.twig');
    }

    #[Route('/poll/{poll}/download-marbles', name: 'poll_marbles_download')]
    public function downloadMarblesCsv(PatreonPoll $poll, #[CurrentUser] User $user): Response
    {
        if (!$poll->getCampaign()->getCampaignOwner()->getId()->equals($user->getId())) {
            throw new AccessDeniedHttpException();
        }

        $options = $this->patreonPollOptionRepository->getOptionsForPoll($poll);
        $csvVotes = [];
        /** @var PatreonPollOption $option */
        foreach ($options as $option) {
            for ($i = 0; $i < $option->getVoteCount(); $i++) {
                $csvVotes[] = $option->getOptionName();
            }
        }

        $response = new Response(implode("\n",$csvVotes));
        $response->headers->set('Content-Type', 'text/csv');
        $disposition = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_INLINE,
            sprintf('%s.csv', $poll->getPollName())
        );
        $response->headers->set('Content-Disposition', $disposition);
        return $response;
    }
}
