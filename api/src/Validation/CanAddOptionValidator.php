<?php

namespace App\Validation;

use App\Entity\PatreonCampaignMember;
use App\Entity\PatreonPoll;
use App\Entity\PatreonPollTierVoteConfig;
use App\Entity\User;
use App\Repository\PatreonCampaignMemberRepository;
use App\Repository\PatreonPollOptionRepository;
use App\Repository\PatreonPollRepository;
use App\Repository\PatreonPollTierVoteConfigRepository;
use App\Repository\PatreonPollVoteRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class CanAddOptionValidator extends ConstraintValidator
{
    public function __construct(
        private readonly Security $security,
        private readonly PatreonPollRepository $patreonPollRepository,
        private readonly PatreonCampaignMemberRepository $campaignMemberRepository,
        private readonly PatreonPollTierVoteConfigRepository $voteConfigRepository,
        private readonly PatreonPollOptionRepository $patreonPollOptionRepository,
    )
    {

    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof CanAddOption) {
            throw new UnexpectedTypeException($constraint, CanAddOption::class);
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

        if ($poll->getEndsAt()?->isPast()) {
            $this->context->buildViolation('Poll has ended')
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

        $myOptions = $this->patreonPollOptionRepository->findMyOptions($poll, $user);

        if ($voteConfig->getMaxOptionAdd() !== 0 && count($myOptions) >= $voteConfig->getMaxOptionAdd()) {
            $this->context->buildViolation(sprintf('You can only add %s options.', $voteConfig->getMaxOptionAdd()))
                ->addViolation();
        }
    }
}
