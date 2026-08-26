<?php

declare(strict_types=1);

namespace Byd\ApiClient\Config;

use Byd\ApiClient\Policy\AuthenticationRetryPolicy;
use Byd\ApiClient\Policy\PollingPolicy;

final readonly class ClientConfig
{
    public function __construct(
        public Credentials $credentials,
        public Locale $locale = new Locale(),
        public DeviceProfile $device = new DeviceProfile(),
        public ProtocolOptions $protocol = new ProtocolOptions(),
        public PollingPolicy $polling = new PollingPolicy(),
        public AuthenticationRetryPolicy $authenticationRetry = new AuthenticationRetryPolicy(),
        public int $sessionTtlSeconds = 43200,
    ) {
    }
}
