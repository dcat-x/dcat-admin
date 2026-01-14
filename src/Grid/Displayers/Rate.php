<?php

namespace Dcat\Admin\Grid\Displayers;

/**
 * Rate Displayer - Display rate/percentage values.
 *
 * Usage:
 * $grid->column('rate')->rate();
 * $grid->column('rate')->rate('%', 2);
 */
class Rate extends AbstractDisplayer
{
    /**
     * Display the rate value with suffix.
     */
    public function display(string $suffix = '%', ?int $decimals = null): string
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
