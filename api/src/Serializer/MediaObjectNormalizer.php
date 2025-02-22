<?php

// api/src/Serializer/MediaObjectNormalizer.php

namespace App\Serializer;

use App\Entity\MediaObject;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class MediaObjectNormalizer implements NormalizerInterface
{

    private const ALREADY_CALLED = 'MEDIA_OBJECT_NORMALIZER_ALREADY_CALLED';

    public function __construct(
        #[Autowire(service: 'api_platform.jsonld.normalizer.item')]
        private readonly NormalizerInterface $normalizer,
        private readonly RouterInterface $router
    ) {
    }

    /**
     * @param MediaObject $data
     * @param string|null $format
     * @param array $context
     * @return array|string|int|float|bool|\ArrayObject|null
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     */
    public function normalize(mixed $data, ?string $format = null, array $context = []): array|string|int|float|bool|\ArrayObject|null
    {
        $context[self::ALREADY_CALLED] = true;

        $data->contentUrl = $this->router->generate('get_media_object', [
            'mediaObject' => $data->getId()
        ], UrlGeneratorInterface::ABSOLUTE_URL);

        return $this->normalizer->normalize($data, $format, $context);
    }

    public function supportsNormalization($data, ?string $format = null, array $context = []): bool
    {

        if (isset($context[self::ALREADY_CALLED])) {
            return false;
        }

        return $data instanceof MediaObject;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            MediaObject::class => true,
        ];
    }
}
