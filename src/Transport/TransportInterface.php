<?php

declare(strict_types=1);

namespace Byd\ApiClient\Transport;

use Byd\ApiClient\Exceptions\BydTransportException;

/**
 * Structural transport interface used by endpoint modules.
 */
interface TransportInterface
{
    /**
     * @param array<string, mixed> $outerPayload
     *
     * @return array<string, mixed>
     *
     * @throws BydTransportException
     */
    public function postSecure(string $endpoint, array $outerPayload): array;
}
