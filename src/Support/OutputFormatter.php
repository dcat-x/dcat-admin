<?php

declare(strict_types=1);

namespace Dcat\Admin\Support;

class OutputFormatter extends \Symfony\Component\Console\Formatter\OutputFormatter
{
    public function format(?string $message): ?string
    {
        return $message;
    }
}
