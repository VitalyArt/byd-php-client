<?php

declare(strict_types=1);

namespace Byd\ApiClient\Models\Control;

use Byd\ApiClient\Models\BaseModel;

class BatteryHeatParams extends BaseModel implements ControlParams
{
    private ?bool $enabled = null;

    /**
     * @param array<string, mixed> $data
     */
    protected function populate(array $data): void
    {
        $this->enabled = isset($data['enabled']) ? (bool) $data['enabled'] : null;
    }

    /**
     * @return array<string, string>
     */
    public function toControlParamsMap(): array
    {
        $params = [];

        if ($this->enabled !== null) {
            $params['batteryHeatSwitch'] = $this->enabled ? 1 : 0;
        }

        return $params;
    }

    public function getEnabled(): ?bool
    {
        return $this->enabled;
    }

    public function setEnabled(?bool $enabled): self
    {
        $this->enabled = $enabled;

        return $this;
    }
}
