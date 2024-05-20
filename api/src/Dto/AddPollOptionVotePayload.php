<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Uuid;

class AddPollOptionVotePayload
{
    #[NotBlank]
    #[Uuid(versions: 7)]
    private ?string $pollId = null;
    #[NotBlank]
    #[Uuid(versions: 7)]
    private ?string $optionId = null;

    public function getPollId(): ?string
    {
        return $this->pollId;
    }

    public function setPollId(?string $pollId): AddPollOptionVotePayload
    {
        $this->pollId = $pollId;
        return $this;
    }

    public function getOptionId(): ?string
    {
        return $this->optionId;
    }

    public function setOptionId(?string $optionId): AddPollOptionVotePayload
    {
        $this->optionId = $optionId;
        return $this;
    }
}
