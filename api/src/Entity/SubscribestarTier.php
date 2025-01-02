<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Link;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Symfony\Action\NotFoundAction;
use App\Repository\SubscribestarTierRepository;
use App\State\Processor\SyncSubscribestarTierProcessor;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;

#[Entity(repositoryClass: SubscribestarTierRepository::class)]
#[ApiResource]
#[Get(controller: NotFoundAction::class, openapi: false)]
#[Post(
    uriTemplate: '/subscribestar_tiers/sync',
    input: false,
    output: false,
    processor: SyncSubscribestarTierProcessor::class,
)]
#[ApiResource(
    uriTemplate: '/subscribestar_users/{id}/tiers',
    operations: [new GetCollection(
        paginationEnabled: false,
        normalizationContext: [
            'groups' => [
                'campaign_tier:read'
            ]
        ]
    )],
    uriVariables: [
        'id' => new Link(
            toProperty: 'subscribestarUser',
            fromClass: SubscribestarUser::class,
            security: "subscribestarUser.getUser() == user"
        )
    ]
)]
class SubscribestarTier extends AbstractCampaignTier
{
    #[ManyToOne(targetEntity: SubscribestarUser::class)]
    #[JoinColumn(nullable: false)]
    private SubscribestarUser $subscribestarUser;
    #[Column(type: 'string', length: 64, unique: true)]
    private string $subscribestarTierId;

    public function getSubscribestarUser(): SubscribestarUser
    {
        return $this->subscribestarUser;
    }

    public function setSubscribestarUser(SubscribestarUser $subscribestarUser): SubscribestarTier
    {
        $this->subscribestarUser = $subscribestarUser;
        return $this;
    }

    public function getSubscribestarTierId(): string
    {
        return $this->subscribestarTierId;
    }

    public function setSubscribestarTierId(string $subscribestarTierId): SubscribestarTier
    {
        $this->subscribestarTierId = $subscribestarTierId;
        return $this;
    }

    function getOwner(): User
    {
        return $this->subscribestarUser->getUser();
    }

    function getVoteConfigClass(): string
    {
        return SubscribestarPollVoteConfig::class;
    }
}
