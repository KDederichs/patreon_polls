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
use App\Entity\PatreonPollVote;
use App\State\EntityToDtoStateProvider;
use Carbon\CarbonImmutable;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints\NotBlank;

#[ApiResource(
    shortName: 'PatreonPollVote',
    provider: EntityToDtoStateProvider::class,
    stateOptions: new Options(entityClass: PatreonPollVote::class)
)]
#[GetCollection(controller: NotFoundAction::class, openapi: false)]
#[Get(controller: NotFoundAction::class, openapi: false)]
#[Post]
#[Delete]
#[ApiResource(
    uriTemplate: '/patreon_poll/{pollId}/my_votes',
    shortName: 'PatreonPollVote',
    operations: [new GetCollection()],
    uriVariables: [
        'pollId' => new Link(toProperty: 'poll', fromClass: PatreonPollApi::class),
    ],
    provider: EntityToDtoStateProvider::class,
    stateOptions: new Options(entityClass: PatreonPollVote::class)
)]
class PatreonPollVoteApi
{
    #[ApiProperty(writable: false, identifier: true)]
    private ?Uuid $id = null;
    #[ApiProperty(writable: false)]
    private ?CarbonImmutable $createdAt = null;
    #[NotBlank]
    private ?PatreonPollOptionApi $option = null;

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function setId(?Uuid $id): PatreonPollVoteApi
    {
        $this->id = $id;
        return $this;
    }

    public function getCreatedAt(): ?CarbonImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?CarbonImmutable $createdAt): PatreonPollVoteApi
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getOption(): ?PatreonPollOptionApi
    {
        return $this->option;
    }

    public function setOption(?PatreonPollOptionApi $option): PatreonPollVoteApi
    {
        $this->option = $option;
        return $this;
    }
}
