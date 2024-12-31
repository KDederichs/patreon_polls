<?php

namespace App\ApiResource;

use ApiPlatform\Doctrine\Orm\State\Options;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use App\Dto\PollVoteConfigDto;
use App\Entity\Poll;
use App\State\EntityClassDtoStateProcessor;
use App\State\EntityToDtoStateProvider;
use Carbon\CarbonImmutable;
use Symfony\Component\Serializer\Attribute\SerializedName;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Valid;

#[ApiResource(
    shortName: 'Poll',
    provider: EntityToDtoStateProvider::class,
    processor: EntityClassDtoStateProcessor::class,
    stateOptions: new Options(entityClass: Poll::class)
)]
#[Get]
#[GetCollection]
#[Post(
    denormalizationContext: [
        'disable_type_enforcement' => true
    ],
    securityPostDenormalize: 'is_granted("create", object)'
)]
class PollApi
{
    #[ApiProperty(writable: false, identifier: true)]
    private ?Uuid $id = null;
    #[ApiProperty(writable: false)]
    private ?CarbonImmutable $createdAt = null;
    #[NotBlank]
    #[Length(max: 255)]
    private ?string $pollName = null;
    private ?CarbonImmutable $endsAt = null;
    private bool $allowPictures = false;
    /**
     * @var array<string, PollVoteConfigDto>
     */
    #[Valid]
    #[NotBlank]
    #[ApiProperty(readable: false)]
    private array $voteConfig = [];
    #[SerializedName('config')]
    #[ApiProperty(readable: true, writable: false)]
    private ?PollVoteConfigDto $voteConfigDto = null;

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function setId(?Uuid $id): PollApi
    {
        $this->id = $id;
        return $this;
    }

    public function getCreatedAt(): ?CarbonImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?CarbonImmutable $createdAt): PollApi
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getPollName(): ?string
    {
        return $this->pollName;
    }

    public function setPollName(?string $pollName): PollApi
    {
        $this->pollName = $pollName;
        return $this;
    }

    public function getEndsAt(): ?CarbonImmutable
    {
        return $this->endsAt;
    }

    public function setEndsAt(?CarbonImmutable $endsAt): PollApi
    {
        $this->endsAt = $endsAt;
        return $this;
    }

    public function isAllowPictures(): bool
    {
        return $this->allowPictures;
    }

    public function setAllowPictures(bool $allowPictures): PollApi
    {
        $this->allowPictures = $allowPictures;
        return $this;
    }

    public function getVoteConfig(): array
    {
        return $this->voteConfig;
    }

    public function setVoteConfig(array $voteConfig): PollApi
    {
        $this->voteConfig = $voteConfig;
        return $this;
    }

    public function getVoteConfigDto(): ?PollVoteConfigDto
    {
        return $this->voteConfigDto;
    }

    public function setVoteConfigDto(?PollVoteConfigDto $voteConfigDto): PollApi
    {
        $this->voteConfigDto = $voteConfigDto;
        return $this;
    }
}
