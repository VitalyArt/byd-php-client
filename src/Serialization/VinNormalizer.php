<?php

declare(strict_types=1);

namespace Byd\ApiClient\Serialization;

use Byd\ApiClient\Value\Vin;
use InvalidArgumentException;

use function is_string;

use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use UnexpectedValueException;

final class VinNormalizer implements NormalizerInterface, DenormalizerInterface
{
    public function normalize(mixed $data, ?string $format = null, array $context = []): string
    {
        if (!$data instanceof Vin) {
            throw new InvalidArgumentException('Expected a VIN.');
        }

        return $data->value;
    }

    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        return $data instanceof Vin;
    }

    public function denormalize(mixed $data, string $type, ?string $format = null, array $context = []): Vin
    {
        if (!is_string($data)) {
            throw new UnexpectedValueException('VIN JSON value must be a string.');
        }

        return new Vin($data);
    }

    public function supportsDenormalization(mixed $data, string $type, ?string $format = null, array $context = []): bool
    {
        return $type === Vin::class;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [Vin::class => true];
    }
}
