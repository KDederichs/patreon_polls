<?php

namespace App\Entity;

use App\Repository\PatreonUserRepository;
use Carbon\CarbonImmutable;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[Entity(repositoryClass: PatreonUserRepository::class)]
class PatreonUser
{
    #[Id, Column(type: UuidType::NAME)]
    private Uuid $id;
    #[Column(type: 'datetime_immutable')]
    private CarbonImmutable $createdAt;
    #[Column(type: 'string', length: 64, unique: true)]
    private string $patreonId;
    #[Column(type: 'string', length: 64, nullable: true)]
    private ?string $patreonAccessToken = null;
    #[Column(type: 'string', length: 64, nullable: true)]
    private ?string $patreonRefreshToken = null;
    #[Column(type: 'text', nullable: true)]
    private ?string $patreonScope = null;
    #[Column(type: 'text', nullable: true)]
    private ?string $patreonTokenType = null;
    #[Column(type: 'datetime_immutable', nullable: true)]
    private ?CarbonImmutable $patreonTokenExpiresAt = null;
    #[Column(type: 'boolean', options: ['default' => false])]
    private bool $creator = false;
    #[ManyToOne(targetEntity: User::class)]
    #[JoinColumn(unique: true, nullable: false)]
    private User $user;
    private string $username;

    public function __construct()
    {
        $this->id = Uuid::v7();
        $this->createdAt = CarbonImmutable::now();
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getCreatedAt(): CarbonImmutable
    {
        return $this->createdAt;
    }

    public function getPatreonId(): ?string
    {
        return $this->patreonId;
    }

    public function setPatreonId(?string $patreonId): PatreonUser
    {
        $this->patreonId = $patreonId;
        return $this;
    }

    public function getPatreonAccessToken(): ?string
    {
        return $this->patreonAccessToken;
    }

    public function setPatreonAccessToken(?string $patreonAccessToken): PatreonUser
    {
        $this->patreonAccessToken = $patreonAccessToken;
        return $this;
    }

    public function getPatreonRefreshToken(): ?string
    {
        return $this->patreonRefreshToken;
    }

    public function setPatreonRefreshToken(?string $patreonRefreshToken): PatreonUser
    {
        $this->patreonRefreshToken = $patreonRefreshToken;
        return $this;
    }

    public function getPatreonScope(): ?string
    {
        return $this->patreonScope;
    }

    public function setPatreonScope(?string $patreonScope): PatreonUser
    {
        $this->patreonScope = $patreonScope;
        return $this;
    }

    public function getPatreonTokenType(): ?string
    {
        return $this->patreonTokenType;
    }

    public function setPatreonTokenType(?string $patreonTokenType): PatreonUser
    {
        $this->patreonTokenType = $patreonTokenType;
        return $this;
    }

    public function getPatreonTokenExpiresAt(): ?CarbonImmutable
    {
        return $this->patreonTokenExpiresAt;
    }

    public function setPatreonTokenExpiresAt(?CarbonImmutable $patreonTokenExpiresAt): PatreonUser
    {
        $this->patreonTokenExpiresAt = $patreonTokenExpiresAt;
        return $this;
    }

    public function isCreator(): bool
    {
        return $this->creator;
    }

    public function setCreator(bool $creator): PatreonUser
    {
        $this->creator = $creator;
        return $this;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): PatreonUser
    {
        $this->user = $user;
        return $this;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): PatreonUser
    {
        $this->username = $username;
        return $this;
    }
}
