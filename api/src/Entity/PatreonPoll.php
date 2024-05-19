<?php

namespace App\Entity;

use Carbon\CarbonImmutable;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[Entity]
class PatreonPoll
{
    #[Id, Column(type: UuidType::NAME)]
    private readonly Uuid $id;
    #[Column(type: 'datetime_immutable')]
    private readonly CarbonImmutable $createdAt;
    #[Column(type: 'text')]
    private string $pollName;
    #[ManyToOne(targetEntity: PatreonCampaign::class)]
    #[JoinColumn(nullable: false)]
    private PatreonCampaign $campaign;
    #[Column(type: 'datetime_immutable', nullable: true)]
    private ?CarbonImmutable $endsAt = null;

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

    public function setPollName(string $pollName): PatreonPoll
    {
        $this->pollName = $pollName;
        return $this;
    }

    public function getEndsAt(): ?CarbonImmutable
    {
        return $this->endsAt;
    }

    public function setEndsAt(?CarbonImmutable $endsAt): PatreonPoll
    {
        $this->endsAt = $endsAt;
        return $this;
    }

    public function getCampaign(): PatreonCampaign
    {
        return $this->campaign;
    }

    public function setCampaign(PatreonCampaign $campaign): PatreonPoll
    {
        $this->campaign = $campaign;
        return $this;
    }
}