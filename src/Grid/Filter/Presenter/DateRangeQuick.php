<?php

namespace Dcat\Admin\Grid\Filter\Presenter;

use Illuminate\Contracts\Support\Arrayable;

class DateRangeQuick extends Presenter
{
    /**
     * @var array
     */
    protected $ranges = [];

    /**
     * @var string
     */
    protected $view = 'admin::filter.date-range-quick';

    /**
     * @var array
     */
    protected $dateOptions = [];

    /**
     * @var bool
     */
    protected $showDateInputs = true;

    /**
     * DateRangeQuick constructor.
     *
     * @param  array  $ranges
     */
    public function __construct($ranges = [])
    {
        if ($ranges instanceof Arrayable) {
            $ranges = $ranges->toArray();
        }

        $this->ranges = (array) $ranges;

        if (empty($this->ranges)) {
            $this->ranges = $this->defaultRanges();
        }
    }

    /**
     * Set date picker options.
     *
     * @param  array  $options
     * @return $this
     */
    public function options($options = [])
    {
        if ($options instanceof Arrayable) {
            $options = $options->toArray();
        }

        $this->dateOptions = array_merge($this->dateOptions, (array) $options);

        return $this;
    }

    /**
     * Set date format.
     *
     * @param  string  $format
     * @return $this
     */
    public function format($format = 'YYYY-MM-DD')
    {
        $this->dateOptions['format'] = $format;

        return $this;
    }

    /**
     * Hide the date input fields.
     *
     * @return $this
     */
    public function hideDateInputs()
    {
        $this->showDateInputs = false;

        return $this;
    }

    /**
     * Default quick ranges.
     *
     * @return array
     */
    protected function defaultRanges()
    {
        $today = date('Y-m-d');
        $yesterday = date('Y-m-d', strtotime('-1 day'));
        $last7days = date('Y-m-d', strtotime('-6 days'));
        $last30days = date('Y-m-d', strtotime('-29 days'));
        $thisMonthStart = date('Y-m-01');
        $lastMonthStart = date('Y-m-01', strtotime('-1 month'));
        $lastMonthEnd = date('Y-m-t', strtotime('-1 month'));

        return [
            trans('admin.today') => [$today, $today],
            trans('admin.yesterday') => [$yesterday, $yesterday],
            trans('admin.last_7_days') => [$last7days, $today],
            trans('admin.last_30_days') => [$last30days, $today],
            trans('admin.this_month') => [$thisMonthStart, $today],
            trans('admin.last_month') => [$lastMonthStart, $lastMonthEnd],
        ];
    }

    public function defaultVariables(): array
    {
        DateTime::requireAssets();

        $format = $this->dateOptions['format'] ?? 'YYYY-MM-DD';
        $this->dateOptions['format'] = $format;
        $this->dateOptions['locale'] = $this->dateOptions['locale'] ?? config('app.locale');

        return [
            'ranges' => $this->ranges,
            'dateOptions' => $this->dateOptions,
            'showDateInputs' => $this->showDateInputs,
        ];
    }
}
