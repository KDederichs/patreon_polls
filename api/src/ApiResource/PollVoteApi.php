<?php

namespace App\ApiResource;

use ApiPlatform\Doctrine\Orm\State\Options;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Link;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Symfony\Action\NotFoundAction;
use App\Entity\PollVote;
use App\State\EntityClassDtoStateProcessor;
use App\State\EntityToDtoStateProvider;
use App\Validator\CanVote;
use Carbon\CarbonImmutable;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints\NotNull;

#[ApiResource(
    shortName: 'PollVote',
    provider: EntityToDtoStateProvider::class,
    processor: EntityClassDtoStateProcessor::class,
    stateOptions: new Options(entityClass: PollVote::class)
)]
#[Get]
#[GetCollection(controller: NotFoundAction::class, openapi: false)]
#[Post(
    securityPostDenormalize: "is_granted('vote', object.getPoll())"
)]
#[Delete]
#[ApiResource(
    uriTemplate: '/polls/{pollId}/my-votes',
    shortName: 'Poll',
    operations: [new GetCollection(
        paginationEnabled: false,
    )],
    uriVariables: [
        'pollId' => new Link(
            toProperty: 'poll',
            fromClass: PollApi::class,
            security: "is_granted('vote', poll)"
        )
    ],
    provider: EntityToDtoStateProvider::class,
    stateOptions: new Options(entityClass: PollVote::class)
)]
#[CanVote]
class PollVoteApi
{
    #[ApiProperty(writable: false, identifier: true)]
    private ?Uuid $id = null;
    #[ApiProperty(writable: false)]
    private ?CarbonImmutable $createdAt = null;
    #[NotNull]
    private ?PollOptionApi $pollOption = null;
    #[ApiProperty(readable: false)]
    private ?PollApi $poll = null;
    #[ApiProperty(writable: false)]
    private ?int $votePower = null;

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function setId(?Uuid $id): PollVoteApi
    {
        $this->id = $id;
        return $this;
    }

    public function getCreatedAt(): ?CarbonImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?CarbonImmutable $createdAt): PollVoteApi
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getPollOption(): ?PollOptionApi
    {
        return $this->pollOption;
    }

    public function setPollOption(?PollOptionApi $pollOption): PollVoteApi
    {
        $this->pollOption = $pollOption;
        return $this;
    }

    public function getPoll(): ?PollApi
    {
        return $this->poll;
    }

    public function setPoll(?PollApi $poll): PollVoteApi
    {
        $this->poll = $poll;
        return $this;
    }

    public function getVotePower(): ?int
    {
        return $this->votePower;
    }

    public function setVotePower(?int $votePower): PollVoteApi
    {
        $this->votePower = $votePower;
        return $this;
    }
}
