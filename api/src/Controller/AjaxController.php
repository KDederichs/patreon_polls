<?php

namespace App\Controller;

use App\Dto\AddPollOptionVotePayload;
use App\Dto\CreatePollOptionPayload;
use App\Dto\RemovePollOptionVotePayload;
use App\Entity\PatreonCampaignMember;
use App\Entity\Poll;
use App\Entity\PollOption;
use App\Entity\PollVoteConfig;
use App\Entity\PollVote;
use App\Entity\User;
use App\Repository\PatreonCampaignMemberRepository;
use App\Repository\PatreonPollOptionRepository;
use App\Repository\PatreonPollRepository;
use App\Repository\PatreonPollTierVoteConfigRepository;
use App\Repository\PatreonPollVoteRepository;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Uid\Uuid;

#[Route('/ajax')]
class AjaxController extends AbstractController
{
    public function __construct(
        private readonly PatreonPollOptionRepository $patreonPollOptionRepository,
        private readonly PatreonPollRepository $patreonPollRepository,
        private readonly PatreonPollVoteRepository $patreonPollVoteRepository,
        private readonly PatreonCampaignMemberRepository $campaignMemberRepository,
        private readonly PatreonPollTierVoteConfigRepository $voteConfigRepository
    )
    {

    }

    #[Route('/poll/option', name: 'ajax_create_poll_option', methods: ['POST'])]
    public function ajaxCreatePollOption(
        #[MapRequestPayload] CreatePollOptionPayload $payload,
        #[CurrentUser] User $user
    ): Response
    {
        /** @var Poll $poll */
        $poll = $this->patreonPollRepository->find(Uuid::fromString($payload->getPollId()));
        /** @var PatreonCampaignMember $member */
        $member = $this->campaignMemberRepository->findByCampaignAndPatreonUserId($poll->getCampaign(), $user->getPatreonId());
        $voteConfig = null;
        if ($member) {
            /** @var PollVoteConfig $voteConfig */
            $voteConfig = $this->voteConfigRepository->findByCampaignTierAndPoll($member->getHighestEntitledTier()->getTier(), $poll);
        }



        $newOption = new PollOption();
        $newOption
            ->setPoll($poll)
            ->setCreatedBy($user)
            ->setOptionName($payload->getOptionName());

        $this->patreonPollOptionRepository->persist($newOption);

        $vote = new PollVote();
        $vote
            ->setOption($newOption)
            ->setPoll($poll)
            ->setVotedBy($user)
            ->setVotePower($voteConfig?->getVotingPower() ?? 1)
        ;
        $this->patreonPollOptionRepository->persist($vote);

        $this->patreonPollOptionRepository->save();

        return new JsonResponse([
            'optionId' => $newOption->getId()->toRfc4122(),
            'voteId' => $vote->getId()->toRfc4122()
        ]);
    }

    #[Route('/poll/option/vote', name: 'ajax_remove_option_vote', methods: ['DELETE'])]
    public function ajaxRemovePollOptionVote(
        #[MapRequestPayload] RemovePollOptionVotePayload $payload,
        #[CurrentUser] User $user
    ): Response
    {
        /** @var PollVote $vote */
        $vote = $this->patreonPollVoteRepository->find(Uuid::fromString($payload->getVoteId()));

        if (!$vote) {
            return new JsonResponse([
                'voteId' => $payload->getVoteId()
            ]);
        }

        if (!$vote->getVotedBy()->getId()->equals($user->getId())) {
            throw new AccessDeniedHttpException('Not your vote');
        }
        $this->patreonPollVoteRepository->remove($vote);
        $this->patreonPollOptionRepository->save();
        return new JsonResponse([
            'voteId' => $vote->getId()->toRfc4122()
        ]);
    }

    #[Route('/poll/option/vote', name: 'ajax_add_option_vote', methods: ['POST'])]
    public function ajaxAddPollOptionVote(
        #[MapRequestPayload] AddPollOptionVotePayload $payload,
        #[CurrentUser] User $user
    ): Response
    {
        /** @var Poll $poll */
        $poll = $this->patreonPollRepository->find(Uuid::fromString($payload->getPollId()));
        /** @var PollOption $option */
        $option = $this->patreonPollOptionRepository->find(Uuid::fromString($payload->getOptionId()));
        /** @var PatreonCampaignMember $member */
        $member = $this->campaignMemberRepository->findByCampaignAndPatreonUserId($poll->getCampaign(), $user->getPatreonId());
        $voteConfig = null;
        if ($member) {
            /** @var PollVoteConfig $voteConfig */
            $voteConfig = $this->voteConfigRepository->findByCampaignTierAndPoll($member->getHighestEntitledTier()->getTier(), $poll);
        }

        try {
            $vote = new PollVote();
            $vote
                ->setOption($option)
                ->setPoll($poll)
                ->setVotedBy($user)
                ->setVotePower($voteConfig?->getVotingPower() ?? 1)
            ;
            $this->patreonPollOptionRepository->persist($vote);

            $this->patreonPollOptionRepository->save();
        } catch (UniqueConstraintViolationException) {
            throw new BadRequestHttpException('You already voted for this option');
        }

        return new JsonResponse([
            'voteId' => $vote->getId()->toRfc4122()
        ]);
    }
}
