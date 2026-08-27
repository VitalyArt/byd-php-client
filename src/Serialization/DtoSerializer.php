<?php

declare(strict_types=1);

namespace Byd\ApiClient\Serialization;

use function array_key_exists;

use Byd\ApiClient\Exception\SerializationException;

use function is_array;
use function is_string;

use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AttributeLoader;
use Symfony\Component\Serializer\NameConverter\MetadataAwareNameConverter;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Throwable;

final class DtoSerializer
{
    private Serializer $serializer;

    public function __construct(?Serializer $serializer = null)
    {
        $metadata = new ClassMetadataFactory(new AttributeLoader());
        $this->serializer = $serializer ?? new Serializer(
            [new VinNormalizer(), new UnknownBackedEnumNormalizer(), new DateTimeNormalizer(), new ObjectNormalizer($metadata, new MetadataAwareNameConverter($metadata))],
            [new JsonEncoder()],
        );
    }

    /** @return array<string, mixed> */
    public function normalize(object $dto): array
    {
        try {
            $data = $this->serializer->normalize($dto, 'json', [
                AbstractObjectNormalizer::SKIP_NULL_VALUES => true,
            ]);
        } catch (Throwable $exception) {
            throw new SerializationException('Unable to serialize '.get_debug_type($dto).'.', $exception->getCode(), previous: $exception);
        }

        if (!is_array($data)) {
            throw new SerializationException('DTO did not normalize to a JSON object.');
        }

        $object = [];
        foreach ($data as $key => $value) {
            if (!is_string($key)) {
                throw new SerializationException('DTO contains a non-string JSON property.');
            }

            $object[$key] = $value;
        }

        return $object;
    }

    /**
     * @template T of object
     * @param array<array-key, mixed> $data
     * @param class-string<T> $type
     * @return T
     */
    public function denormalize(array $data, string $type): object
    {
        $data = $this->canonicalize($data);

        try {
            $dto = $this->serializer->denormalize($data, $type, 'json', [
                AbstractNormalizer::ALLOW_EXTRA_ATTRIBUTES => true,
                AbstractNormalizer::DEFAULT_CONSTRUCTOR_ARGUMENTS => [$type => ['raw' => $data]],
            ]);
        } catch (Throwable $exception) {
            throw new SerializationException("Unable to deserialize {$type}.", $exception->getCode(), previous: $exception);
        }

        if (!$dto instanceof $type) {
            throw new SerializationException("Serializer returned an unexpected type for {$type}.");
        }

        return $dto;
    }

    /**
     * @param array<array-key, mixed> $data
     * @return array<string, mixed>
     */
    private function canonicalize(array $data): array
    {
        $object = [];
        foreach ($data as $key => $value) {
            if (is_string($key)) {
                $object[$key] = $value;
            }
        }

        $data = $object;
        $aliases = [
            'backCover' => 'trunkLid',
            'stearingWheelHeatState' => 'steeringWheelHeatState',
            'leftFrontTirepressure' => 'leftFrontTirePressure',
            'rightFrontTirepressure' => 'rightFrontTirePressure',
            'leftRearTirepressure' => 'leftRearTirePressure',
            'rightRearTirepressure' => 'rightRearTirePressure',
            'abs' => 'absWarning',
            'time' => 'timestamp',
        ];

        foreach ($aliases as $alias => $canonical) {
            if (array_key_exists($alias, $data) && !array_key_exists($canonical, $data)) {
                $data[$canonical] = $data[$alias];
            }
        }

        // Some vehicle-list responses return image URLs only inside cfPic.
        // Flatten those legacy fields so Vehicle DTO consumers get the same
        // mainImageUrl/imageSetUrl values regardless of response shape.
        if (is_array($data['cfPic'] ?? null)) {
            foreach (['picMainUrl', 'picSetUrl'] as $field) {
                if (!array_key_exists($field, $data) && array_key_exists($field, $data['cfPic'])) {
                    $data[$field] = $data['cfPic'][$field];
                }
            }
        }

        $chargingState = $data['chargingState'] ?? null;
        if (isset($data['chargeState']) && is_numeric($data['chargeState']) && (!is_numeric($chargingState) || (float) $chargingState < 0.0)) {
            $data['chargingState'] = $data['chargeState'];
        }

        foreach (['tempInCar', 'tempOutCar'] as $field) {
            if (isset($data[$field]) && is_numeric($data[$field]) && (float) $data[$field] <= -100.0) {
                $data[$field] = null;
            }
        }

        foreach (['fullHour', 'fullMinute', 'remainingHours', 'remainingMinutes', 'oilEndurance', 'ectValue'] as $field) {
            if (isset($data[$field]) && is_numeric($data[$field]) && (float) $data[$field] < 0) {
                $data[$field] = null;
            }
        }

        return $data;
    }
}
