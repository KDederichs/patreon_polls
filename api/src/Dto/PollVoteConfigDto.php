<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Range;
use Symfony\Component\Validator\Constraints\When;

class PollVoteConfigDto
{
    #[When(
        expression: 'this.getCanAddOptions()',
        constraints: [
            new NotBlank(),
            new Range(min: 1)
        ]
    )]
    private ?int $numberOfOptions = null;
    #[When(
        expression: 'this.getHasLimitedVotes()',
        constraints: [
            new NotBlank(),
            new Range(min: 1)
        ]
    )]
    private ?int $numberOfVotes = null;
    #[NotBlank]
    #[Range(min: 1)]
    private ?int $votingPower = null;
    private bool $canAddOptions = false;
    private bool $hasLimitedVotes = false;

    public function getNumberOfOptions(): ?int
    {
        return $this->numberOfOptions;
    }

    public function setNumberOfOptions(?int $numberOfOptions): PollVoteConfigDto
    {
        $this->numberOfOptions = $numberOfOptions;
        return $this;
    }

    public function getNumberOfVotes(): ?int
    {
        return $this->numberOfVotes;
    }

    public function setNumberOfVotes(?int $numberOfVotes): PollVoteConfigDto
    {
        $this->numberOfVotes = $numberOfVotes;
        return $this;
    }

    public function getVotingPower(): ?int
    {
        return $this->votingPower;
    }

    public function setVotingPower(?int $votingPower): PollVoteConfigDto
    {
        $this->votingPower = $votingPower;
        return $this;
    }

    public function getCanAddOptions(): bool
    {
        return $this->canAddOptions;
    }

    public function setCanAddOptions(bool $canAddOptions): PollVoteConfigDto
    {
        $this->canAddOptions = $canAddOptions;
        return $this;
    }

    public function getHasLimitedVotes(): bool
    {
        return $this->hasLimitedVotes;
    }

    public function setHasLimitedVotes(bool $hasLimitedVotes): PollVoteConfigDto
    {
        $this->hasLimitedVotes = $hasLimitedVotes;
        return $this;
    }
}
