<?php

namespace App\ApiResource;

use ApiPlatform\Action\NotFoundAction;
use ApiPlatform\Doctrine\Orm\State\Options;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use App\Entity\PatreonPoll;
use App\State\EntityToDtoStateProvider;
use Carbon\CarbonImmutable;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

#[ApiResource(
    shortName: 'PatreonPoll',
    provider: EntityToDtoStateProvider::class,
    stateOptions: new Options(entityClass: PatreonPoll::class)
)]
#[GetCollection(controller: NotFoundAction::class, openapi: false)]
#[Get]
#[Post]
#[Delete]
class PatreonPollApi {

    #[ApiProperty(writable: false, identifier: true)]
    private ?Uuid $id = null;
    #[ApiProperty(writable: false)]
    private ?CarbonImmutable $createdAt = null;
    #[NotBlank]
    #[Length(min: 1, max: 255)]
    private ?string $pollName = null;
    private ?CarbonImmutable $endsAt = null;
    #[ApiProperty(readable: false)]
    private ?PatreonCampaignApi $campaign = null;
    #[ApiProperty(readable: false)]
    private array $tierSettings = [];

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function setId(?Uuid $id): PatreonPollApi
    {
        $this->id = $id;
        return $this;
    }

    public function getCreatedAt(): ?CarbonImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?CarbonImmutable $createdAt): PatreonPollApi
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getPollName(): ?string
    {
        return $this->pollName;
    }

    public function setPollName(?string $pollName): PatreonPollApi
    {
        $this->pollName = $pollName;
        return $this;
    }

    public function getEndsAt(): ?CarbonImmutable
    {
        return $this->endsAt;
    }

    public function setEndsAt(?CarbonImmutable $endsAt): PatreonPollApi
    {
        $this->endsAt = $endsAt;
        return $this;
    }

    public function getCampaign(): ?PatreonCampaignApi
    {
        return $this->campaign;
    }

    public function setCampaign(?PatreonCampaignApi $campaign): PatreonPollApi
    {
        $this->campaign = $campaign;
        return $this;
    }
}
