<?php

declare(strict_types=1);

namespace Byd\ApiClient\Tests;

use Byd\ApiClient\BydClient;
use Byd\ApiClient\Value\Vin;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use Symfony\Component\Serializer\Attribute\Ignore;
use Symfony\Component\Serializer\Attribute\SerializedName;

final class ArchitectureTest extends TestCase
{
    #[DataProvider('dtoClasses')]
    public function testEveryDtoPropertyDeclaresWireMapping(string $class): void
    {
        self::assertTrue(class_exists($class));
        $reflection = new ReflectionClass($class);
        self::assertTrue($reflection->isReadOnly(), $class.' must be readonly.');
        foreach ($reflection->getProperties() as $property) {
            $mapped = $property->getAttributes(SerializedName::class) !== [] || $property->getAttributes(Ignore::class) !== [];
            self::assertTrue($mapped, $class.'::$'.$property->getName().' has no serializer attribute.');
        }
    }

    /** @return iterable<string, array{class-string}> */
    public static function dtoClasses(): iterable
    {
        foreach (array_merge(glob(__DIR__.'/../src/Dto/Request/*.php') ?: [], glob(__DIR__.'/../src/Dto/Response/*.php') ?: [], glob(__DIR__.'/../src/Dto/Protocol/*.php') ?: []) as $file) {
            $source = file_get_contents($file);
            if ($source === false || preg_match('/namespace\s+([^;]+);.*?class\s+(\w+)/s', $source, $match) !== 1) {
                continue;
            }

            $class = $match[1].'\\'.$match[2];
            if (!class_exists($class)) {
                continue;
            }

            yield $class => [$class];
        }
    }

    public function testLegacyArchitectureWasRemoved(): void
    {
        self::assertSame([], glob(__DIR__.'/../src/Api/*.php') ?: []);
        self::assertSame([], glob(__DIR__.'/../src/Models/*.php') ?: []);
        self::assertFileDoesNotExist(__DIR__.'/../src/Client.php');
        self::assertFileDoesNotExist(__DIR__.'/../src/VehicleContext.php');
    }

    public function testClientExposesVinScopedServicesDirectly(): void
    {
        $client = new ReflectionClass(BydClient::class);

        foreach (['telemetry', 'climate', 'charging', 'controls', 'notifications', 'settings', 'ota'] as $methodName) {
            $method = $client->getMethod($methodName);
            $parameters = $method->getParameters();

            self::assertCount(1, $parameters, $methodName.'() must accept only a VIN.');
            self::assertSame(Vin::class, (string) $parameters[0]->getType());
        }

        self::assertFalse($client->hasMethod('forVehicle'));
    }

    public function testResourceServicesDoNotExposeStaticOperations(): void
    {
        foreach (glob(__DIR__.'/../src/Service/*.php') ?: [] as $file) {
            $source = file_get_contents($file);
            self::assertIsString($source);
            self::assertStringNotContainsString('public static function', $source, $file);
        }
    }
}
