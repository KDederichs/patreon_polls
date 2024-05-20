<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Uuid;

class CreatePollOptionPayload
{
    #[NotBlank]
    #[Uuid(versions: 7)]
    private ?string $pollId = null;
    #[NotBlank]
    #[Length(min: 3, max: 255)]
    private ?string $optionName = null;

    public function getPollId(): ?string
    {
        return $this->pollId;
    }

    public function setPollId(?string $pollId): CreatePollOptionPayload
    {
        $this->pollId = $pollId;
        return $this;
    }

    public function getOptionName(): ?string
    {
        return $this->optionName;
    }

    public function setOptionName(?string $optionName): CreatePollOptionPayload
    {
        $this->optionName = $optionName;
        return $this;
    }


}
