<?php

declare(strict_types=1);

namespace Byd\ApiClient\Serialization;

use BackedEnum;
use InvalidArgumentException;

use function is_scalar;

use ReflectionEnum;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use UnexpectedValueException;

final class UnknownBackedEnumNormalizer implements NormalizerInterface, DenormalizerInterface
{
    public function normalize(mixed $data, ?string $format = null, array $context = []): string|int
    {
        if (!$data instanceof BackedEnum) {
            throw new InvalidArgumentException('Expected a backed enum.');
        }

        return $data->value;
    }

    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        return $data instanceof BackedEnum;
    }

    public function denormalize(mixed $data, string $type, ?string $format = null, array $context = []): BackedEnum
    {
        if (!is_subclass_of($type, BackedEnum::class)) {
            throw new InvalidArgumentException("{$type} is not a backed enum.");
        }

        if (!is_scalar($data)) {
            throw new UnexpectedValueException("Non-scalar value for {$type}.");
        }

        $reflection = new ReflectionEnum($type);
        $value = $reflection->getBackingType()?->getName() === 'int' && is_numeric($data) ? (int) $data : (string) $data;
        $enum = $type::tryFrom($value);
        if ($enum instanceof BackedEnum) {
            return $enum;
        }

        foreach ($type::cases() as $case) {
            if ($case->name === 'UNKNOWN') {
                return $case;
            }
        }

        throw new UnexpectedValueException("Unknown value for {$type}.");
    }

    public function supportsDenormalization(mixed $data, string $type, ?string $format = null, array $context = []): bool
    {
        return enum_exists($type) && is_subclass_of($type, BackedEnum::class);
    }

    public function getSupportedTypes(?string $format): array
    {
        return [BackedEnum::class => true];
    }
}
