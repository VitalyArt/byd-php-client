<?php

declare(strict_types=1);

namespace Byd\ApiClient\Dto\Request;

use Byd\ApiClient\Value\Vin;
use SensitiveParameter;
use Symfony\Component\Serializer\Attribute\SerializedName;

final readonly class VerifyControlPinRequest
{
    public function __construct(
        #[SerializedName('vin')]
        public Vin $vin,
        #[SerializedName('commandPwd')]
        #[SensitiveParameter]
        public string $commandPassword,
        #[SerializedName('functionType')]
        public string $functionType = 'remoteControl',
    ) {
    }
}
