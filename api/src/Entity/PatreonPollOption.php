<?php

namespace App\Entity;

use Carbon\CarbonImmutable;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\OneToOne;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[Entity]
class PatreonPollOption
{
    #[Id, Column(type: UuidType::NAME)]
    private readonly Uuid $id;
    #[Column(type: 'datetime_immutable')]
    private readonly CarbonImmutable $createdAt;
    #[ManyToOne(targetEntity: PatreonPoll::class)]
    #[JoinColumn(nullable: false)]
    private PatreonPoll $poll;
    #[Column(type: 'text')]
    private string $optionName;
    #[ManyToOne(targetEntity: User::class)]
    #[JoinColumn(nullable: false)]
    private User $createdBy;
    #[OneToOne(targetEntity: MediaObject::class, orphanRemoval: true)]
    private ?MediaObject $mediaObject = null;

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

    public function getPoll(): PatreonPoll
    {
        return $this->poll;
    }

    public function setPoll(PatreonPoll $poll): PatreonPollOption
    {
        $this->poll = $poll;
        return $this;
    }

    public function getOptionName(): string
    {
        return $this->optionName;
    }

    public function setOptionName(string $optionName): PatreonPollOption
    {
        $this->optionName = $optionName;
        return $this;
    }

    public function getCreatedBy(): User
    {
        return $this->createdBy;
    }

    public function setCreatedBy(User $createdBy): PatreonPollOption
    {
        $this->createdBy = $createdBy;
        return $this;
    }

    public function getMediaObject(): ?MediaObject
    {
        return $this->mediaObject;
    }

    public function setMediaObject(?MediaObject $mediaObject): PatreonPollOption
    {
        $this->mediaObject = $mediaObject;
        return $this;
    }
}
