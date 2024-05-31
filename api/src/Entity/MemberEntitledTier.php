<?php

namespace App\Entity;

use Carbon\CarbonImmutable;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\ManyToOne;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[Entity(readOnly: true)]
class MemberEntitledTier
{
    #[Id, Column(type: UuidType::NAME)]
    private Uuid $id;
    #[Column(type: 'datetime_immutable')]
    private CarbonImmutable $createdAt;
    #[ManyToOne(targetEntity: PatreonCampaignMember::class, inversedBy: 'entitledTiers')]
    private PatreonCampaignMember $campaignMember;
    #[ManyToOne(targetEntity: PatreonCampaignTier::class, fetch: 'EAGER')]
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

    public function getCampaignMember(): PatreonCampaignMember
    {
        return $this->campaignMember;
    }

    public function setCampaignMember(PatreonCampaignMember $campaignMember): MemberEntitledTier
    {
        $this->campaignMember = $campaignMember;
        return $this;
    }

    public function getTier(): PatreonCampaignTier
    {
        return $this->tier;
    }

    public function setTier(PatreonCampaignTier $tier): MemberEntitledTier
    {
        $this->tier = $tier;
        return $this;
    }

    public function __toString(): string
    {
        return sprintf("%s -> %s", $this->campaignMember->getPatreonUserId(), $this->tier->getTierName());
    }
}
