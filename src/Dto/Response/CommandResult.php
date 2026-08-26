<?php

declare(strict_types=1);

namespace Byd\ApiClient\Dto\Response;

use function array_key_exists;

use Symfony\Component\Serializer\Attribute\Ignore;
use Symfony\Component\Serializer\Attribute\SerializedName;

final readonly class CommandResult
{
    /** @param array<string, mixed> $raw */
    public function __construct(
        #[SerializedName('code')]
        public string|int $code = '0',
        #[SerializedName('message')]
        public string $message = '',
        #[SerializedName('requestSerial')]
        public ?string $requestSerial = null,
        #[SerializedName('controlState')]
        public ?int $controlState = null,
        #[SerializedName('res')]
        public ?int $result = null,
        #[SerializedName('commandType')]
        public ?string $commandType = null,
        #[Ignore]
        public array $raw = [],
    ) {
    }

    public function isSuccess(): bool
    {
        return (string) $this->code === '0' && $this->controlState !== 2 && ($this->result === null || $this->result >= 2);
    }

    public function isTerminal(): bool
    {
        return $this->controlState !== null && $this->controlState !== 0 || $this->result !== null && $this->result >= 2 || array_key_exists('result', $this->raw);
    }
}
