<?php

namespace App\Entity;

use App\Repository\PatreonCampaignMemberRepository;
use Carbon\CarbonImmutable;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\UniqueConstraint;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[Entity(repositoryClass: PatreonCampaignMemberRepository::class)]
#[UniqueConstraint(fields: ['campaign','patreonUserId'])]
class PatreonCampaignMember
{
    #[Id, Column(type: UuidType::NAME)]
    private readonly Uuid $id;
    #[Column(type: 'datetime_immutable')]
    private readonly CarbonImmutable $createdAt;
    #[Column(type: 'string', length: 64)]
    private string $patreonUserId;
    #[ManyToOne(targetEntity: PatreonCampaign::class)]
    #[JoinColumn(nullable: false)]
    private PatreonCampaign $campaign;
    #[ManyToOne(targetEntity: PatreonCampaignTier::class)]
    #[JoinColumn(nullable: false)]
    private PatreonCampaignTier $tier;

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

    public function getPatreonUserId(): string
    {
        return $this->patreonUserId;
    }

    public function setPatreonUserId(string $patreonUserId): PatreonCampaignMember
    {
        $this->patreonUserId = $patreonUserId;
        return $this;
    }

    public function getCampaign(): PatreonCampaign
    {
        return $this->campaign;
    }

    public function setCampaign(PatreonCampaign $campaign): PatreonCampaignMember
    {
        $this->campaign = $campaign;
        return $this;
    }

    public function getTier(): PatreonCampaignTier
    {
        return $this->tier;
    }

    public function setTier(PatreonCampaignTier $tier): PatreonCampaignMember
    {
        $this->tier = $tier;
        return $this;
    }
}
