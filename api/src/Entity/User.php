<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Carbon\CarbonImmutable;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Table;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Uid\Uuid;

#[Table(name: 'users')]
#[Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface
{

    #[Id, Column(type: UuidType::NAME)]
    private Uuid $id;
    #[Column(type: 'datetime_immutable')]
    private CarbonImmutable $createdAt;
    #[Column(type: 'string', length: 64, unique: true, nullable: true)]
    private ?string $patreonId = null;
    #[Column(type: 'string', length: 64, nullable: true)]
    private ?string $patreonAccessToken = null;
    #[Column(type: 'string', length: 64, nullable: true)]
    private ?string $patreonRefreshToken = null;
    #[Column(type: 'text', nullable: true)]
    private ?string $patreonScope = null;
    #[Column(type: 'text', nullable: true)]
    private ?string $patreonTokenType = null;
    #[Column(type: 'text')]
    private string $username;
    #[Column(type: 'datetime_immutable', nullable: true)]
    private ?CarbonImmutable $patreonTokenExpiresAt = null;
    #[Column(type: 'boolean', options: ['default' => false])]
    private bool $creator = false;

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

    public function getRoles(): array
    {
        return ['ROLE_USER'];
    }

    public function eraseCredentials(): void
    {

    }

    public function getUserIdentifier(): string
    {
        return $this->id->toRfc4122();
    }

    public function getPatreonId(): ?string
    {
        return $this->patreonId;
    }

    public function setPatreonId(?string $patreonId): User
    {
        $this->patreonId = $patreonId;
        return $this;
    }

    public function getPatreonAccessToken(): ?string
    {
        return $this->patreonAccessToken;
    }

    public function setPatreonAccessToken(?string $patreonAccessToken): User
    {
        $this->patreonAccessToken = $patreonAccessToken;
        return $this;
    }

    public function getPatreonRefreshToken(): ?string
    {
        return $this->patreonRefreshToken;
    }

    public function setPatreonRefreshToken(?string $patreonRefreshToken): User
    {
        $this->patreonRefreshToken = $patreonRefreshToken;
        return $this;
    }

    public function getPatreonScope(): ?string
    {
        return $this->patreonScope;
    }

    public function setPatreonScope(?string $patreonScope): User
    {
        $this->patreonScope = $patreonScope;
        return $this;
    }

    public function getPatreonTokenType(): ?string
    {
        return $this->patreonTokenType;
    }

    public function setPatreonTokenType(?string $patreonTokenType): User
    {
        $this->patreonTokenType = $patreonTokenType;
        return $this;
    }

    public function getPatreonTokenExpiresAt(): ?CarbonImmutable
    {
        return $this->patreonTokenExpiresAt;
    }

    public function setPatreonTokenExpiresAt(?CarbonImmutable $patreonTokenExpiresAt): User
    {
        $this->patreonTokenExpiresAt = $patreonTokenExpiresAt;
        return $this;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): User
    {
        $this->username = $username;
        return $this;
    }

    public function __toString(): string
    {
        return $this->username;
    }

    public function isCreator(): bool
    {
        return $this->creator;
    }

    public function setCreator(bool $creator): User
    {
        $this->creator = $creator;
        return $this;
    }
}
