<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Valid;

class CreatePollInput
{
    #[NotBlank]
    #[Length(max: 255)]
    private ?string $pollName = null;
    #[NotBlank]
    private ?\DateTimeImmutable $endDate = null;
    private bool $allowPictures = false;
    /**
     * @var array<string, PollVoteConfigDto>
     */
    #[Valid]
    private array $voteConfig = [];

    public function getPollName(): ?string
    {
        return $this->pollName;
    }

    public function setPollName(?string $pollName): CreatePollInput
    {
        $this->pollName = $pollName;
        return $this;
    }

    public function getEndDate(): ?\DateTimeImmutable
    {
        return $this->endDate;
    }

    public function setEndDate(?\DateTimeImmutable $endDate): CreatePollInput
    {
        $this->endDate = $endDate;
        return $this;
    }

    public function isAllowPictures(): bool
    {
        return $this->allowPictures;
    }

    public function setAllowPictures(bool $allowPictures): CreatePollInput
    {
        $this->allowPictures = $allowPictures;
        return $this;
    }

    /**
     * @return array<string, PollVoteConfigDto>
     */
    public function getVoteConfig(): array
    {
        return $this->voteConfig;
    }

    /**
     * @param array<string, PollVoteConfigDto> $voteConfig
     * @return $this
     */
    public function setVoteConfig(array $voteConfig): CreatePollInput
    {
        $this->voteConfig = $voteConfig;
        return $this;
    }


}
