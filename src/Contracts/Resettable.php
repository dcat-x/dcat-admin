<?php

declare(strict_types=1);

namespace Dcat\Admin\Contracts;

interface Resettable
{
    /**
     * Reset static state for Octane/long-lived process compatibility.
     */
    public static function resetState(): void;
}
