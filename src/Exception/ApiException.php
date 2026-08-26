<?php

declare(strict_types=1);

namespace Byd\ApiClient\Exception;

use Byd\ApiClient\Enum\ApiErrorCode;
use Byd\ApiClient\Enum\Endpoint;
use Throwable;

class ApiException extends BydException
{
    public function __construct(
        string $message,
        public readonly int $apiCode,
        public readonly Endpoint $endpoint,
        public readonly ?ApiErrorCode $knownCode = null,
        ?Throwable $previous = null,
    ) {
        parent::__construct($message, $apiCode, $previous);
    }
}
