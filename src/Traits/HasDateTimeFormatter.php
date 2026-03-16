<?php

declare(strict_types=1);

namespace Dcat\Admin\Traits;

trait HasDateTimeFormatter
{
    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->format($this->getDateFormat());
    }
}
