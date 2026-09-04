<?php

declare(strict_types=1);

namespace Byd\ApiClient\Exception;

use Byd\ApiClient\Enum\WatchEndpoint;
use Throwable;

final class WatchApiException extends BydException
{
    public function __construct(
        string $message,
        public readonly int $apiCode,
        public readonly WatchEndpoint $endpoint,
        ?Throwable $previous = null,
    ) {
        parent::__construct($message, $apiCode, $previous);
    }
}
