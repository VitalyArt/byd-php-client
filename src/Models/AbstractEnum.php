<?php

declare(strict_types=1);

namespace Byd\ApiClient\Models;

use function in_array;

use ReflectionClass;

/**
 * Abstract base class for enums.
 */
abstract class AbstractEnum
{
    protected static array $cache = [];

    /**
     * Get all constants in the enum.
     *
     * @return array<string, int>
     */
    public static function getAll(): array
    {
        $class = static::class;
        if (!isset(self::$cache[$class])) {
            $reflection = new ReflectionClass($class);
            self::$cache[$class] = $reflection->getConstants();
        }

        return self::$cache[$class];
    }

    /**
     * Check if a value exists in the enum.
     */
    public static function isValid(int $value): bool
    {
        return in_array($value, static::getAll(), true);
    }

    /**
     * Get the name of a value.
     */
    public static function getName(int $value): ?string
    {
        $constants = array_flip(static::getAll());

        return $constants[$value] ?? null;
    }

    /**
     * Get the value for a name.
     */
    public static function getValue(string $name): ?int
    {
        $constants = static::getAll();

        return $constants[$name] ?? null;
    }
}
