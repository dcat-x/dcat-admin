<?php

namespace Dcat\Admin\Grid\Filter;

use Dcat\Admin\Grid\Filter\Presenter\Toggle as TogglePresenter;
use Illuminate\Support\Arr;

class Toggle extends AbstractFilter
{
    /**
     * {@inheritdoc}
     */
    protected $view = 'admin::filter.toggle';

    /**
     * @var mixed
     */
    protected $onValue = 1;

    /**
     * @var mixed
     */
    protected $offValue = 0;

    /**
     * Setup default presenter.
     *
     * @return void
     */
    protected function setupDefaultPresenter()
    {
        $this->setPresenter(new TogglePresenter);
    }

    /**
     * Set on/off values.
     *
     * @param  mixed  $on
     * @param  mixed  $off
     * @return $this
     */
    public function values($on, $off = 0)
    {
        $this->onValue = $on;
        $this->offValue = $off;

        return $this;
    }

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

        return $this->buildCondition($this->column, $this->value);
    }
}
