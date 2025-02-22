<?php

namespace App\Entity;

use App\Enum\OAuthAuthType;
use App\Enum\OAuthProvider;
use App\Repository\OAuthStateRepository;
use Carbon\CarbonImmutable;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\ManyToOne;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[Entity(repositoryClass: OAuthStateRepository::class)]
class OauthState
{
    #[Id, Column(type: UuidType::NAME)]
    private readonly Uuid $id;
    #[Column(type: 'datetime_immutable')]
    private readonly CarbonImmutable $createdAt;
    #[Column(enumType: OAuthAuthType::class)]
    private OAuthAuthType $authType = OAuthAuthType::Login;
    #[Column(enumType: OAuthProvider::class)]
    private OAuthProvider $provider = OAuthProvider::Patreon;
    #[ManyToOne(targetEntity: User::class)]
    private ?User $user = null;

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

    public function getAuthType(): OAuthAuthType
    {
        return $this->authType;
    }

    public function setAuthType(OAuthAuthType $authType): OauthState
    {
        $this->authType = $authType;
        return $this;
    }

    public function getProvider(): OAuthProvider
    {
        return $this->provider;
    }

    public function setProvider(OAuthProvider $provider): OauthState
    {
        $this->provider = $provider;
        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): OauthState
    {
        $this->user = $user;
        return $this;
    }
}
