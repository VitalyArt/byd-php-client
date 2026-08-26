<?php

declare(strict_types=1);

namespace Byd\ApiClient\Policy;

use Byd\ApiClient\Exception\ValidationException;

final readonly class PollingPolicy
{
    public function __construct(
        public int $maximumAttempts = 10,
        public int $intervalMilliseconds = 1500,
        public int $timeoutMilliseconds = 15000,
    ) {
        if ($maximumAttempts < 1 || $intervalMilliseconds < 0 || $timeoutMilliseconds < 1) {
            throw new ValidationException('Polling values must be positive.');
        }
    }
}
