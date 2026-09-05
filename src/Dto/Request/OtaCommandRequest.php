<?php

declare(strict_types=1);

namespace Byd\ApiClient\Dto\Request;

use Byd\ApiClient\Value\Vin;
use SensitiveParameter;
use Symfony\Component\Serializer\Attribute\SerializedName;

final readonly class OtaCommandRequest
{
    public function __construct(
        #[SerializedName('vin')]
        public Vin $vin,
        #[SerializedName('language')]
        public string $language,
        #[SerializedName('commandPwd')]
        #[SensitiveParameter]
        public string $commandPassword,
    ) {
    }
}
