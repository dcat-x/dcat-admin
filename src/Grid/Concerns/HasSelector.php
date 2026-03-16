<?php

declare(strict_types=1);

namespace Dcat\Admin\Grid\Concerns;

use Dcat\Admin\Grid;
use Dcat\Admin\Grid\Tools\Selector;
use Dcat\Admin\Support\Helper;

/**
 * @mixin Grid
 */
trait HasSelector
{
    /**
     * @var Selector|null
     */
    protected $_selector;

    /**
     * @return $this|Selector
     */
    public function selector(?\Closure $closure = null)
    {
        if ($closure === null) {
            return $this->_selector;
        }

        $this->_selector = new Selector($this);

        $this->invokeSelectorBuilder($closure);

        $this->header(function () {
            return $this->renderSelector();
        });

        return $this;
    }

    /**
     * Apply selector query to grid model query.
     *
     * @return $this
     */
    protected function applySelectorQuery()
    {
        if (is_null($this->_selector)) {
            return $this;
        }

        $active = $this->_selector->parseSelected();

        $this->_selector->all()->each(function ($selector, $column) use ($active) {
            $key = $this->_selector->formatKey($column);

            if (! array_key_exists($key, $active)) {
                return;
            }

            $this->fireOnce(new Grid\Events\ApplySelector([$active]));

            $values = $active[$key];
            if ($selector['type'] == 'one') {
                $values = current($values);
            }

            if ($selector['query']) {
                $this->invokeSelectorQuery($selector['query'], $values);

                return;
            }

            Helper::withQueryCondition(
                $this->model(),
                $column,
                is_array($values) ? 'whereIn' : 'where',
                [$values]
            );
        });

        return $this;
    }

    /**
     * Render grid selector.
     *
     * @return \Illuminate\Contracts\View\View|string
     */
    public function renderSelector()
    {
        return $this->_selector->render();
    }

    protected function invokeSelectorBuilder(\Closure $closure): void
    {
        $closure($this->_selector);
    }

    protected function invokeSelectorQuery(callable $query, $values): void
    {
        $query($this->model(), $values);
    }
}
