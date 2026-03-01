<?php

declare(strict_types=1);

namespace Byd\ApiClient\Models\Control;

interface ControlParams
{
    /**
     * Convert to control params map for API requests.
     *
     * @return array<string, string>
     */
    public function toControlParamsMap(): array;
}
