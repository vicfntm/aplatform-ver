<?php

declare(strict_types=1);


namespace App\Convertors;


use App\Entity\Media;
use JetBrains\PhpStorm\NoReturn;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Vich\UploaderBundle\Storage\StorageInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class MediaNormalizer implements NormalizerInterface
{

    private const ALREADY_CALLED = 'MEDIA_OBJECT_NORMALIZER_ALREADY_CALLED';

    public function __construct(private readonly StorageInterface $storage, private readonly ObjectNormalizer $normalizer)
    {
    }

    /**
     * @throws ExceptionInterface
     */
    #[NoReturn]
    public function normalize($object, ?string $format = null, array $context = []): array|string|int|float|bool|\ArrayObject|null
    {
        $context[self::ALREADY_CALLED] = true;
        $object->setBinarySource($this->storage->resolveUri($object, 'file'));

        return $this->normalizer->normalize($object, $format, $context);
    }

    public function supportsNormalization($data, string $format = null, array $context = []): bool
    {
        return $data instanceof Media;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            'object'     => null,             // Doesn't support any classes or interfaces
            '*'          => false,                 // Supports any other types, but the result is not cacheable
            Media::class => true, // Supports MyCustomClass and result is cacheable
        ];
    }
}
