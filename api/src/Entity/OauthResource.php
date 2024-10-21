<?php

namespace App\Entity;

use Carbon\CarbonImmutable;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\MappedSuperclass;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[MappedSuperclass]
abstract class OauthResource
{
    #[Id, Column(type: UuidType::NAME)]
    private Uuid $id;
    #[Column(type: 'datetime_immutable')]
    private CarbonImmutable $createdAt;
    #[Column(type: 'string', length: 64, unique: true)]
    private string $resourceId;
    #[Column(type: 'string', length: 64, nullable: true)]
    private ?string $accessToken = null;
    #[Column(type: 'string', length: 64, nullable: true)]
    private ?string $refreshToken = null;
    #[Column(type: 'text', nullable: true)]
    private ?string $scope = null;
    #[Column(type: 'text', nullable: true)]
    private ?string $tokenType = null;
    #[Column(type: 'datetime_immutable', nullable: true)]
    private ?CarbonImmutable $accessTokenExpiresAt = null;
    #[Column(type: 'boolean', options: ['default' => false])]
    private bool $creator = false;
    #[ManyToOne(targetEntity: User::class)]
    #[JoinColumn(nullable: false)]
    private User $user;
    #[Column(type: 'text', nullable: true)]
    private ?string $username = null;

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

    public function getResourceId(): string
    {
        return $this->resourceId;
    }

    public function setResourceId(string $resourceId): OauthResource
    {
        $this->resourceId = $resourceId;
        return $this;
    }

    public function getAccessToken(): ?string
    {
        return $this->accessToken;
    }

    public function setAccessToken(?string $accessToken): OauthResource
    {
        $this->accessToken = $accessToken;
        return $this;
    }

    public function getRefreshToken(): ?string
    {
        return $this->refreshToken;
    }

    public function setRefreshToken(?string $refreshToken): OauthResource
    {
        $this->refreshToken = $refreshToken;
        return $this;
    }

    public function getScope(): ?string
    {
        return $this->scope;
    }

    public function setScope(?string $scope): OauthResource
    {
        $this->scope = $scope;
        return $this;
    }

    public function getTokenType(): ?string
    {
        return $this->tokenType;
    }

    public function setTokenType(?string $tokenType): OauthResource
    {
        $this->tokenType = $tokenType;
        return $this;
    }

    public function getAccessTokenExpiresAt(): ?CarbonImmutable
    {
        return $this->accessTokenExpiresAt;
    }

    public function setAccessTokenExpiresAt(?CarbonImmutable $accessTokenExpiresAt): OauthResource
    {
        $this->accessTokenExpiresAt = $accessTokenExpiresAt;
        return $this;
    }

    public function isCreator(): bool
    {
        return $this->creator;
    }

    public function setCreator(bool $creator): OauthResource
    {
        $this->creator = $creator;
        return $this;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): OauthResource
    {
        $this->user = $user;
        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(?string $username): OauthResource
    {
        $this->username = $username;
        return $this;
    }


}
