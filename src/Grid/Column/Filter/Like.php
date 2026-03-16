<?php

declare(strict_types=1);

namespace Dcat\Admin\Grid\Column\Filter;

use Dcat\Admin\Grid\Model;

class Like extends Equal
{
    /**
     * Add a binding to the query.
     *
     * @param  string  $value
     */
    public function addBinding($value, Model $model)
    {
        $value = trim((string) $value);
        if ($value === '') {
            return;
        }

        $this->withQuery($model, 'where', ['like', "%{$value}%"]);
    }
}
