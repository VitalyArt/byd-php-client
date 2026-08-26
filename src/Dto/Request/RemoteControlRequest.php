<?php

declare(strict_types=1);

namespace Byd\ApiClient\Dto\Request;

use Byd\ApiClient\Enum\RemoteCommand;
use Byd\ApiClient\Value\Vin;
use SensitiveParameter;
use Symfony\Component\Serializer\Attribute\SerializedName;

final readonly class RemoteControlRequest
{
    /** @param array<string, mixed>|null $parameters */
    public function __construct(
        #[SerializedName('vin')]
        public Vin $vin,
        #[SerializedName('commandType')]
        public RemoteCommand $command,
        #[SerializedName('commandPwd')]
        #[SensitiveParameter]
        public string $commandPassword = '',
        #[SerializedName('controlParamsMap')]
        public ?array $parameters = null,
        #[SerializedName('requestSerial')]
        public ?string $requestSerial = null,
    ) {
    }
}
