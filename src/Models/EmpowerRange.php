<?php

declare(strict_types=1);

namespace Byd\ApiClient\Models;

use function is_array;

/**
 * A permission scope granted to a shared user.
 */
class EmpowerRange extends BaseModel
{
    private string $code = '';

    private string $name = '';

    /**
     * @var EmpowerRange[]
     */
    private array $children = [];

    /**
     * @param array<string, mixed> $data
     */
    protected function populate(array $data): void
    {
        $this->code = (string) ($data['code'] ?? '');
        $this->name = (string) ($data['name'] ?? '');

        // Handle children - look for both 'children' and 'childList'
        $childrenData = $data['children'] ?? $data['childList'] ?? [];
        if (is_array($childrenData)) {
            foreach ($childrenData as $childData) {
                $this->children[] = new EmpowerRange($childData);
            }
        }
    }

    // Getters
    public function getCode(): string
    {
        return $this->code;
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return EmpowerRange[]
     */
    public function getChildren(): array
    {
        return $this->children;
    }
}
