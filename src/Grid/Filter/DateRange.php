<?php

namespace Dcat\Admin\Grid\Filter;

use Dcat\Admin\Grid\Filter\Presenter\DateRangeQuick;
use Illuminate\Support\Arr;

class DateRange extends AbstractFilter
{
    /**
     * {@inheritdoc}
     */
    protected $view = 'admin::filter.date-range';

    /**
     * @var int
     */
    protected $width = 12;

    /**
     * @var bool
     */
    protected $timestamp = false;

    /**
     * Convert the datetime into unix timestamp.
     *
     * @return $this
     */
    public function toTimestamp()
    {
        $this->timestamp = true;

        return $this;
    }

    /**
     * Setup default presenter.
     *
     * @return void
     */
    protected function setupDefaultPresenter()
    {
        $this->setPresenter(new DateRangeQuick);
    }

    /**
     * Use quick date range selector with custom ranges.
     *
     * @param  array  $ranges
     * @return DateRangeQuick
     */
    public function quick($ranges = [])
    {
        return $this->setPresenter(new DateRangeQuick($ranges));
    }

    /**
     * Format id.
     *
     * @param  string  $column
     * @return array|string
     */
    public function formatId($column)
    {
        $id = str_replace('.', '_', $column);
        $prefix = $this->parent->grid()->makeName('filter-column-');

        return ['start' => "{$prefix}{$id}-start", 'end' => "{$prefix}{$id}-end"];
    }

    /**
     * Format two field names of this filter.
     *
     * @param  string  $column
     * @return array
     */
    protected function formatName($column)
    {
        $columns = explode('.', $column);

        if (count($columns) == 1) {
            $name = $this->parent->grid()->makeName($columns[0]);
        } else {
            $name = $this->parent->grid()->makeName(array_shift($columns));

            foreach ($columns as $column) {
                $name .= "[$column]";
            }
        }

        return ['start' => "{$name}[start]", 'end' => "{$name}[end]"];
    }

    /**
     * Get condition of this filter.
     *
     * @param  array  $inputs
     * @return mixed
     */
    public function condition($inputs)
    {
        if (! Arr::has($inputs, $this->column)) {
            return;
        }

        $this->value = Arr::get($inputs, $this->column);

        $value = array_filter($this->value, function ($val) {
            return $val !== '';
        });

        if ($this->timestamp) {
            $value = array_map(function ($v) {
                if ($v) {
                    return strtotime($v);
                }
            }, $value);
        }

        if (empty($value)) {
            return;
        }

        if (! isset($value['start']) && isset($value['end'])) {
            return $this->buildCondition($this->column, '<=', $value['end']);
        }

        if (! isset($value['end']) && isset($value['start'])) {
            return $this->buildCondition($this->column, '>=', $value['start']);
        }

        $this->query = 'whereBetween';

        return $this->buildCondition($this->column, [$value['start'], $value['end']]);
    }
}
