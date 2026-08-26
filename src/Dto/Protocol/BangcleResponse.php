<?php

declare(strict_types=1);

namespace Byd\ApiClient\Dto\Protocol;

use SensitiveParameter;
use Symfony\Component\Serializer\Attribute\SerializedName;

final readonly class BangcleResponse
{
    public function __construct(#[SerializedName('response')] #[SensitiveParameter] public string $response)
    {
    }
}
