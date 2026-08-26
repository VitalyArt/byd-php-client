<?php

declare(strict_types=1);

namespace Byd\ApiClient\Dto\Protocol;

use SensitiveParameter;
use Symfony\Component\Serializer\Attribute\SerializedName;

final readonly class BangcleRequest
{
    public function __construct(#[SerializedName('request')] #[SensitiveParameter] public string $request)
    {
    }
}
