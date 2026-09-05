<?php

declare(strict_types=1);

namespace Byd\ApiClient\Dto\Request;

use Byd\ApiClient\Value\Vin;
use Symfony\Component\Serializer\Attribute\SerializedName;

final readonly class OtaVersionRequest
{
    public function __construct(
        #[SerializedName('vin')]
        public Vin $vin,
        #[SerializedName('language')]
        public string $language,
    ) {
    }
}
