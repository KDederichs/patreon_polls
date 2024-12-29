<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use App\Dto\CreatePollInput;
use App\Repository\PollRepository;
use App\State\Processor\CreatePollProcessor;
use Carbon\CarbonImmutable;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\ManyToOne;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Uid\Uuid;

#[Entity(repositoryClass: PollRepository::class)]
#[ApiResource(
    normalizationContext: [
        'groups' => [
            'poll:read'
        ]
    ]
)]
#[Get]
#[GetCollection]
#[Post(
    denormalizationContext: [
        'disable_type_enforcement' => true
    ],
    securityPostDenormalize: 'is_granted("create", object)',
    input: CreatePollInput::class,
    processor: CreatePollProcessor::class
)]
class Poll
{
    #[Id, Column(type: UuidType::NAME)]
    #[Groups(['poll:read'])]
    private Uuid $id;
    #[Column(type: 'datetime_immutable')]
    #[Groups(['poll:read'])]
    private CarbonImmutable $createdAt;
    #[Column(type: 'text')]
    #[Groups(['poll:read'])]
    private string $pollName;
    #[Column(type: 'datetime_immutable', nullable: true)]
    #[Groups(['poll:read'])]
    private ?CarbonImmutable $endsAt = null;
    #[ManyToOne(targetEntity: User::class)]
    private User $createdBy;

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

    public function getPollName(): string
    {
        return $this->pollName;
    }

    public function setPollName(string $pollName): Poll
    {
        $this->pollName = $pollName;
        return $this;
    }

    public function getEndsAt(): ?CarbonImmutable
    {
        return $this->endsAt;
    }

    public function setEndsAt(?\DateTimeImmutable $endsAt): Poll
    {
        $this->endsAt = CarbonImmutable::instance($endsAt);
        return $this;
    }

    public function __toString(): string
    {
        return $this->pollName;
    }

    public function getCreatedBy(): User
    {
        return $this->createdBy;
    }

    public function setCreatedBy(User $createdBy): Poll
    {
        $this->createdBy = $createdBy;
        return $this;
    }

    public function getUser(): User
    {
        return $this->getCreatedBy();
    }
}
