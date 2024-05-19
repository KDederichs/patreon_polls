<?php

namespace App\ApiResource;

use ApiPlatform\Action\NotFoundAction;
use ApiPlatform\Doctrine\Orm\State\Options;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Link;
use ApiPlatform\Metadata\Post;
use App\Entity\PatreonPollOption;
use App\State\EntityToDtoStateProvider;
use Carbon\CarbonImmutable;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

#[ApiResource(
    shortName: 'PatreonPollOption',
    provider: EntityToDtoStateProvider::class,
    stateOptions: new Options(entityClass: PatreonPollOption::class)
)]
#[GetCollection(controller: NotFoundAction::class, openapi: false)]
#[Get(controller: NotFoundAction::class, openapi: false)]
#[Post]
#[ApiResource(
    uriTemplate: '/patreon_poll/{pollId}/options',
    shortName: 'PatreonPollOption',
    operations: [new GetCollection()],
    uriVariables: [
        'pollId' => new Link(toProperty: 'poll', fromClass: PatreonPollApi::class),
    ],
    provider: EntityToDtoStateProvider::class,
    stateOptions: new Options(entityClass: PatreonPollOption::class)
)]
class PatreonPollOptionApi
{
    #[ApiProperty(writable: false, identifier: true)]
    private ?Uuid $id = null;
    #[ApiProperty(writable: false)]
    private ?CarbonImmutable $createdAt = null;
    #[NotBlank]
    #[Length(min: 1, max: 255)]
    private ?string $optionName = null;
    #[ApiProperty(writable: false)]
    private ?string $imageUrl = null;
    #[ApiProperty(writable: false)]
    private int $numberOfVotes = 0;

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function setId(?Uuid $id): PatreonPollOptionApi
    {
        $this->id = $id;
        return $this;
    }

    public function getCreatedAt(): ?CarbonImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?CarbonImmutable $createdAt): PatreonPollOptionApi
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getOptionName(): ?string
    {
        return $this->optionName;
    }

    public function setOptionName(?string $optionName): PatreonPollOptionApi
    {
        $this->optionName = $optionName;
        return $this;
    }

    public function getImageUrl(): ?string
    {
        return $this->imageUrl;
    }

    public function setImageUrl(?string $imageUrl): PatreonPollOptionApi
    {
        $this->imageUrl = $imageUrl;
        return $this;
    }

    public function getNumberOfVotes(): int
    {
        return $this->numberOfVotes;
    }

    public function setNumberOfVotes(int $numberOfVotes): PatreonPollOptionApi
    {
        $this->numberOfVotes = $numberOfVotes;
        return $this;
    }
}
