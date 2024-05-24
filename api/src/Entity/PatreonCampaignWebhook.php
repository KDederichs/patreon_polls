<?php

namespace App\Entity;

use App\Repository\PatreonCampaignWebhookRepository;
use Carbon\CarbonImmutable;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[Entity(repositoryClass: PatreonCampaignWebhookRepository::class)]
class PatreonCampaignWebhook
{
    #[Id, Column(type: UuidType::NAME)]
    private Uuid $id;
    #[Column(type: 'datetime_immutable')]
    private CarbonImmutable $createdAt;
    #[Column(type: 'string', length: 64, unique: true)]
    private string $patreonWebhookId;
    #[Column(type: 'json')]
    private array $triggers = [];
    #[Column(type: 'text')]
    private string $secret;
    #[ManyToOne(targetEntity: PatreonCampaign::class)]
    #[JoinColumn(nullable: false)]
    private PatreonCampaign $campaign;

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

    public function getPatreonWebhookId(): string
    {
        return $this->patreonWebhookId;
    }

    public function setPatreonWebhookId(string $patreonWebhookId): PatreonCampaignWebhook
    {
        $this->patreonWebhookId = $patreonWebhookId;
        return $this;
    }

    public function getTriggers(): array
    {
        return $this->triggers;
    }

    public function setTriggers(array $triggers): PatreonCampaignWebhook
    {
        $this->triggers = $triggers;
        return $this;
    }

    public function getSecret(): string
    {
        return $this->secret;
    }

    public function setSecret(string $secret): PatreonCampaignWebhook
    {
        $this->secret = $secret;
        return $this;
    }

    public function getCampaign(): PatreonCampaign
    {
        return $this->campaign;
    }

    public function setCampaign(PatreonCampaign $campaign): PatreonCampaignWebhook
    {
        $this->campaign = $campaign;
        return $this;
    }
}
