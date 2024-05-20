<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Uuid;

class RemovePollOptionVotePayload
{
    #[NotBlank]
    #[Uuid(versions: 7)]
    private ?string $voteId = null;

    public function getVoteId(): ?string
    {
        return $this->voteId;
    }

    public function setVoteId(?string $voteId): RemovePollOptionVotePayload
    {
        $this->voteId = $voteId;
        return $this;
    }
}
