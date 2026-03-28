<?php

declare(strict_types=1);

namespace Byd\ApiClient\Models;

/**
 * Base model for BYD API responses.
 */
class BaseModel
{
    /**
     * @param array<string, mixed> $raw
     */
    public function __construct(protected array $raw = [])
    {
        $this->populate($this->raw);
    }

    /**
     * @param array<string, mixed> $data
     */
    protected function populate(array $data): void
    {
        // This method should be overridden in child classes
        // to map API data to model properties
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $result = [];
        foreach (get_object_vars($this) as $key => $value) {
            if ($key !== 'raw') {
                $result[$this->snakeToCamel($key)] = $value;
            }
        }

        return $result;
    }

    /**
     * Convert snake_case to camelCase.
     */
    protected function snakeToCamel(string $str): string
    {
        return lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $str))));
    }

    /**
     * Convert camelCase to snake_case.
     */
    protected function camelToSnake(string $str): string
    {
        return strtolower((string) preg_replace('/(?<!^)[A-Z]/', '_$0', $str));
    }

    /**
     * @return array<string, mixed>
     */
    public function getRaw(): array
    {
        return $this->raw;
    }
}
