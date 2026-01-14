<?php

namespace Dcat\Admin\Grid\Filter;

use Illuminate\Support\Arr;

class WhereNotNull extends AbstractFilter
{
    /**
     * @var string
     */
    protected $query = 'whereNotNull';

    /**
     * Get condition of this filter.
     *
     * @param  array  $inputs
     * @return mixed
     */
    public function condition($inputs)
    {
        $value = Arr::get($inputs, $this->column);

        if ($value === null || $value === '') {
            return;
        }

        $this->value = $value;

        if ($value) {
            return $this->buildCondition($this->column);
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function buildCondition(...$params)
    {
        if ($this->ignore) {
            return;
        }

        $column = explode('.', $this->column);

        if (count($column) == 1) {
            return [$this->query => [$this->column]];
        }

        return $this->buildRelationQuery(...$params);
    }
}
