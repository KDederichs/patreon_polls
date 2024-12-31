<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Symfony\Action\NotFoundAction;
use App\Repository\UserRepository;
use Carbon\CarbonImmutable;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\Table;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Uid\Uuid;

#[Table(name: 'users')]
#[Entity(repositoryClass: UserRepository::class)]
#[ApiResource]
#[Get(
    security: "object.getId().equals(user.getId())"
)]
#[GetCollection(controller: NotFoundAction::class, openapi: false)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{

    #[Id, Column(type: UuidType::NAME)]
    private Uuid $id;
    #[Column(type: 'datetime_immutable')]
    private CarbonImmutable $createdAt;
    #[Column(type: 'string', length: 64, unique: true, nullable: true)]
    private ?string $patreonId = null;
    #[Column(type: 'text', nullable: true)]
    private ?string $username = null;
    #[Column(type: 'boolean', options: ['default' => false])]
    private bool $admin = false;
    #[OneToMany(targetEntity: ApiToken::class, mappedBy: 'ownedBy', cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $apiTokens;

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
        return $this->admin ? ['ROLE_ADMIN', 'ROLE_USER']:['ROLE_USER'];
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

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(?string $username): User
    {
        $this->username = $username;
        return $this;
    }

    public function __toString(): string
    {
        return $this->username ?? $this->id->toRfc4122();
    }

    public function isAdmin(): bool
    {
        return $this->admin;
    }

    public function setAdmin(bool $admin): User
    {
        $this->admin = $admin;
        return $this;
    }

    public function getApiTokens(): Collection
    {
        return $this->apiTokens;
    }

    public function setApiTokens(Collection $apiTokens): User
    {
        $this->apiTokens = $apiTokens;
        return $this;
    }

    public function getPassword(): ?string
    {
        // I only need the interface
        return null;
    }
}
