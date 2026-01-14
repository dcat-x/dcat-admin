<?php

namespace Dcat\Admin\Show\Fields;

use Dcat\Admin\Show\AbstractField;

/**
 * Rate Field - Display rate/percentage values in Show page.
 *
 * Usage:
 * $show->field('rate')->rate();
 * $show->field('rate')->rate('%', 2);
 */
class Rate extends AbstractField
{
    /**
     * Render the rate value with suffix.
     */
    public function render(string $suffix = '%', ?int $decimals = null): string
    {
        if ($this->value === null || $this->value === '') {
            return '0'.$suffix;
        }

        $value = $decimals !== null
            ? number_format((float) $this->value, $decimals, '.', '')
            : $this->value;

        return $value.$suffix;
    }
}
