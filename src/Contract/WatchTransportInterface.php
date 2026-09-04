<?php

declare(strict_types=1);

namespace Byd\ApiClient\Contract;

use Byd\ApiClient\Enum\WatchEndpoint;

interface WatchTransportInterface
{
    /** @return array<string, mixed> */
    public function send(WatchEndpoint $endpoint, object $request): array;
}
