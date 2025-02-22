<?php

namespace App\Dto\OAuth;

readonly class OAuthIdentity
{
    public function __construct(
        private string $id,
        private ?string $username,
    )
    {

    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }
}
