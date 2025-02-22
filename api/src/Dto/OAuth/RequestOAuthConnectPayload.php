<?php

namespace App\Dto\OAuth;

use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Uuid;

readonly class RequestOAuthConnectPayload
{
    public function __construct(
        #[NotBlank]
        #[Choice(choices: ['user', 'creator'])]
        private ?string $mode = null,
    )
    {

    }

    public function getMode(): ?string
    {
        return $this->mode;
    }




}
