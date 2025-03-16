<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Symfony\Action\NotFoundAction;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\NotNull;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use ApiPlatform\OpenApi\Model;

#[Entity]
#[Vich\Uploadable]
#[ApiResource(
    types: ['https://schema.org/MediaObject'],
    operations: [
        new Get(),
        new GetCollection(controller: NotFoundAction::class, openapi: false),
        new Post(
            inputFormats: ['multipart' => ['multipart/form-data']],
            openapi: new Model\Operation(
                requestBody: new Model\RequestBody(
                    content: new \ArrayObject([
                        'multipart/form-data' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'file' => [
                                        'type' => 'string',
                                        'format' => 'binary'
                                    ]
                                ]
                            ]
                        ]
                    ])
                )
            )
        ),
        new Delete(
            security: 'user.getId().equals(object.getUploadedBy().getId())'
        )
    ],
    outputFormats: ['jsonld' => ['application/ld+json']],
    normalizationContext: ['groups' => ['media_object:read']]
)]
class MediaObject
{
    #[Id, Column(type: UuidType::NAME)]
    private Uuid $id;
    #[Vich\UploadableField(
        mapping: 'media',
        fileNameProperty: 'fileName',
        size: 'fileSize',
        mimeType: 'mimeType',
        originalName: 'originalName',
       dimensions: 'dimensions'
    )]
    #[NotNull]
    #[Assert\File(
        maxSize: '3M',
        mimeTypes: [
            'image/jpeg',
            'image/png',
            'image/webp',
        ],
    )]
    private ?File $file = null;
    #[Column(nullable: true)]
    #[ApiProperty(writable: false)]
    private ?string $fileName = null;
    #[Column(nullable: true)]
    #[ApiProperty(writable: false)]
    private ?int $fileSize = null;
    #[Column(nullable: true)]
    #[ApiProperty(writable: false)]
    private ?string $mimeType = null;
    #[Column(type: 'json')]
    #[ApiProperty(writable: false)]
    private array $dimensions = [];
    #[Column(nullable: true)]
    #[ApiProperty(writable: false)]
    private ?string $originalName = null;
    #[Column(nullable: true)]
    #[ApiProperty(writable: false)]
    private ?\DateTimeImmutable $updatedAt = null;
    #[ApiProperty(types: ['https://schema.org/contentUrl'], writable: false)]
    #[Groups(['media_object:read'])]
    public ?string $contentUrl = null;
    #[ManyToOne(targetEntity: User::class)]
    #[JoinColumn(nullable: false)]
    #[ApiProperty(writable: false)]
    private ?User $uploadedBy = null;

    public function __construct()
    {
        $this->id = Uuid::v7();
    }


    public function getId(): Uuid
    {
        return $this->id;
    }

    public static function fromFile(File $file): MediaObject
    {
        $mediaObject = new MediaObject();
        $mediaObject->setFile($file);

        return $mediaObject;
    }


    public function getFile(): ?File
    {
        return $this->file;
    }

    public function setFile(?File $file): MediaObject
    {
        $this->file = $file;
        if (null !== $file) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new \DateTimeImmutable();
        }
        return $this;
    }

    public function getFileName(): ?string
    {
        return $this->fileName;
    }

    public function setFileName(?string $fileName): MediaObject
    {
        $this->fileName = $fileName;
        return $this;
    }

    public function getFileSize(): ?int
    {
        return $this->fileSize;
    }

    public function setFileSize(?int $fileSize): MediaObject
    {
        $this->fileSize = $fileSize;
        return $this;
    }

    public function getMimeType(): ?string
    {
        return $this->mimeType;
    }

    public function setMimeType(?string $mimeType): MediaObject
    {
        $this->mimeType = $mimeType;
        return $this;
    }

    public function getDimensions(): array
    {
        return $this->dimensions;
    }

    public function setDimensions(array $dimensions): MediaObject
    {
        $this->dimensions = $dimensions;
        return $this;
    }

    public function getOriginalName(): ?string
    {
        return $this->originalName;
    }

    public function setOriginalName(?string $originalName): MediaObject
    {
        $this->originalName = $originalName;
        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): MediaObject
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    public function __toString(): string
    {
        $name = $this->originalName ?? $this->fileName;

        return $name ?? $this->id->toRfc4122();
    }

    public function getContentUrl(): ?string
    {
        return $this->contentUrl;
    }

    public function setContentUrl(?string $contentUrl): MediaObject
    {
        $this->contentUrl = $contentUrl;
        return $this;
    }

    public function getUploadedBy(): ?User
    {
        return $this->uploadedBy;
    }

    public function setUploadedBy(?User $uploadedBy): MediaObject
    {
        $this->uploadedBy = $uploadedBy;
        return $this;
    }

    public function getWidth(): ?int
    {
        return $this->dimensions[0] ?? null;
    }

    public function getHeight(): ?int
    {
        return $this->dimensions[1] ?? null;
    }

    #[Groups(['media_object:read'])]
    public function getImageOrientation(): string
    {
        $width = $this->getWidth();
        $height = $this->getHeight();

        if (null === $width || null === $height) {
            return 'portrait';
        }

        if ($width === $height) {
            return 'square';
        }

        if ($width > $height) {
            return 'landscape';
        }
        return 'portrait';
    }
}
