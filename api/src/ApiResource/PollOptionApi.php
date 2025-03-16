<?php

namespace App\ApiResource;

use ApiPlatform\Doctrine\Orm\State\Options;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Link;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Symfony\Action\NotFoundAction;
use App\Entity\MediaObject;
use App\Entity\PollOption;
use App\State\EntityClassDtoStateProcessor;
use App\State\EntityToDtoStateProvider;
use App\Validator\CanAddOption;
use Carbon\CarbonImmutable;
use Symfony\Component\Uid\Uuid;

#[ApiResource(
    shortName: 'PollOption',
    provider: EntityToDtoStateProvider::class,
    processor: EntityClassDtoStateProcessor::class,
    stateOptions: new Options(entityClass: PollOption::class)
)]
#[Get(controller: NotFoundAction::class, openapi: false)]
#[GetCollection(controller: NotFoundAction::class, openapi: false)]
#[Post(
    securityPostDenormalize: "is_granted('vote', object.getPoll())"
)]
#[ApiResource(
    uriTemplate: '/polls/{pollId}/options',
    shortName: 'Poll',
    operations: [new GetCollection(
        paginationEnabled: false
    )],
    uriVariables: [
        'pollId' => new Link(toProperty: 'poll', fromClass: PollApi::class),
    ],
    provider: EntityToDtoStateProvider::class,
    stateOptions: new Options(entityClass: PollOption::class)
)]
#[CanAddOption]
class PollOptionApi
{
    #[ApiProperty(writable: false, identifier: true)]
    private ?Uuid $id = null;
    #[ApiProperty(writable: false)]
    private ?CarbonImmutable $createdAt = null;
    #[ApiProperty(readable: false)]
    private ?PollApi $poll = null;
    private ?string $optionName = null;
    #[ApiProperty(writable: false)]
    private int $numberOfVotes = 0;
    #[ApiProperty(readable: false,types: ['https://schema.org/image'])]
    public ?MediaObject $image = null;
    private ?string $imageUri = null;
    private bool $myOption = false;
    #[ApiProperty(writable: false)]
    private string $imageOrientation = 'unknown';

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function setId(?Uuid $id): PollOptionApi
    {
        $this->id = $id;
        return $this;
    }

    public function getCreatedAt(): ?CarbonImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?CarbonImmutable $createdAt): PollOptionApi
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getPoll(): ?PollApi
    {
        return $this->poll;
    }

    public function setPoll(?PollApi $poll): PollOptionApi
    {
        $this->poll = $poll;
        return $this;
    }

    public function getOptionName(): ?string
    {
        return $this->optionName;
    }

    public function setOptionName(?string $optionName): PollOptionApi
    {
        $this->optionName = $optionName;
        return $this;
    }

    public function getNumberOfVotes(): int
    {
        return $this->numberOfVotes;
    }

    public function setNumberOfVotes(int $numberOfVotes): PollOptionApi
    {
        $this->numberOfVotes = $numberOfVotes;
        return $this;
    }

    public function getImage(): ?MediaObject
    {
        return $this->image;
    }

    public function setImage(?MediaObject $image): PollOptionApi
    {
        $this->image = $image;
        return $this;
    }

    public function getImageUri(): ?string
    {
        return $this->imageUri;
    }

    public function setImageUri(?string $imageUri): PollOptionApi
    {
        $this->imageUri = $imageUri;
        return $this;
    }

    public function isMyOption(): bool
    {
        return $this->myOption;
    }

    public function setMyOption(bool $myOption): PollOptionApi
    {
        $this->myOption = $myOption;
        return $this;
    }

    public function getImageOrientation(): string
    {
        return $this->imageOrientation;
    }

    public function setImageOrientation(string $imageOrientation): PollOptionApi
    {
        $this->imageOrientation = $imageOrientation;
        return $this;
    }
}
