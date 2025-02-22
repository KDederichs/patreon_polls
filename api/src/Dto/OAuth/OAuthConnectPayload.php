<?php

namespace App\Dto\OAuth;

use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Uuid;

readonly class OAuthConnectPayload
{
    public function __construct(
        #[NotBlank]
        private ?string $code = null,
        #[NotBlank]
        #[Uuid(versions: 7)]
        private ?string $state = null,
    )
    {

    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function getState(): ?string
    {
        return $this->state;
    }


}
