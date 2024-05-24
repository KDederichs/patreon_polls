<?php

namespace App\Validation;

use App\Entity\PatreonCampaignMember;
use App\Entity\PatreonPoll;
use App\Entity\PatreonPollTierVoteConfig;
use App\Entity\User;
use App\Repository\PatreonCampaignMemberRepository;
use App\Repository\PatreonPollRepository;
use App\Repository\PatreonPollTierVoteConfigRepository;
use App\Repository\PatreonPollVoteRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class CanVoteValidator extends ConstraintValidator
{
    public function __construct(
        private readonly Security $security,
        private readonly PatreonPollRepository $patreonPollRepository,
        private readonly PatreonPollVoteRepository $patreonPollVoteRepository,
        private readonly PatreonCampaignMemberRepository $campaignMemberRepository,
        private readonly PatreonPollTierVoteConfigRepository $voteConfigRepository
    )
    {

    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof CanVote) {
            throw new UnexpectedTypeException($constraint, CanVote::class);
        }

        if (empty($value)) {
            return;
        }

        if (!Uuid::isValid($value)) {
            return;
        }

        /** @var PatreonPoll | null $poll */
        $poll = $this->patreonPollRepository->find(Uuid::fromString($value));
        if (!$poll) {
            $this->context->buildViolation('Unknown poll')
                ->addViolation();
            return;
        }

        /** @var User $user */
        $user = $this->security->getUser();

        /** @var PatreonCampaignMember | null $member */
        $member = $this->campaignMemberRepository->findByCampaignAndPatreonUserId($poll->getCampaign(), $user->getPatreonId());
        if (!$member) {
            $this->context->buildViolation('You are not a member of '. $poll->getCampaign()->getCampaignName())
                ->addViolation();
            return;
        }
        $tierEntitlement = $member->getHighestEntitledTier();
        if (!$tierEntitlement) {
            $this->context->buildViolation('You do not have a reward tier.')
                ->addViolation();
            return;
        }

        /** @var PatreonPollTierVoteConfig|null $voteConfig */
        $voteConfig = $this->voteConfigRepository->findByCampaignTierAndPoll($tierEntitlement->getTier(), $poll);

        if (!$voteConfig) {
            $this->context->buildViolation('Your reward tier can not vote on this poll. Please upgrade to a higher tier.')
                ->addViolation();
            return;
        }

        if ($voteConfig->getNumberOfVotes() === 0) {
            return;
        }

        $votes = $this->patreonPollVoteRepository->findMyVotes($user, $poll);

        if (count($votes) >= $voteConfig->getNumberOfVotes()) {
            $this->context->buildViolation('You have already reached the maximum vote count of: '. $voteConfig->getNumberOfVotes())
                ->addViolation();
        }
    }
}
