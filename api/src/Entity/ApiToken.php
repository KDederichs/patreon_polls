<?php

namespace App\Entity;

use App\Repository\ApiTokenRepository;
use Carbon\CarbonImmutable;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[Entity(repositoryClass: ApiTokenRepository::class)]
class ApiToken
{
    #[Id, Column(type: UuidType::NAME)]
    private Uuid $id;
    #[Column(type: 'datetime_immutable')]
    private CarbonImmutable $createdAt;
    #[ManyToOne(targetEntity: User::class, inversedBy: 'apiTokens')]
    #[JoinColumn(nullable: false)]
    private User $ownedBy;
    #[Column(type: 'datetime_immutable', nullable: true)]
    private ?CarbonImmutable $expiresAt = null;
    #[Column(type: 'string', length: 64, unique: true)]
    private string $token;
    private string $tokenPlain;

    public function __construct()
    {
        $this->id = Uuid::v7();
        $this->createdAt = CarbonImmutable::now();
        $this->tokenPlain = bin2hex(random_bytes(32));
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getCreatedAt(): CarbonImmutable
    {
        return $this->createdAt;
    }

    public function setToken(string $token): ApiToken
    {
        $this->token = $token;
        return $this;
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function getOwnedBy(): User
    {
        return $this->ownedBy;
    }

    public function setOwnedBy(User $ownedBy): ApiToken
    {
        $this->ownedBy = $ownedBy;
        return $this;
    }

    public function setExpiresAt(?CarbonImmutable $expiresAt): ApiToken
    {
        $this->expiresAt = $expiresAt;
        return $this;
    }

    public function getExpiresAt(): ?CarbonImmutable
    {
        return $this->expiresAt;
    }

    public function getTokenPlain(): string
    {
        return $this->tokenPlain;
    }

    public function isValid(): bool
    {
        if (!$this->expiresAt) {
            return true;
        }

        return $this->expiresAt->isFuture();
    }
}
