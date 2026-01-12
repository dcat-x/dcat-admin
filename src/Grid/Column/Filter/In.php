<?php

namespace Dcat\Admin\Grid\Column\Filter;

use Dcat\Admin\Grid\Column\Filter;
use Dcat\Admin\Grid\Model;

class In extends Filter
{
    use Checkbox;

    /**
     * @var array
     */
    protected $options = [];

    /**
     * CheckFilter constructor.
     */
    public function __construct(array $options)
    {
        $this->options = $options;

        $this->class = [
            'all' => uniqid('column-filter-all-'),
            'item' => uniqid('column-filter-item-'),
        ];
    }

    /**
     * Add a binding to the query.
     *
     * @param  array  $value
     */
    public function addBinding($value, Model $model)
    {
        if (empty($value)) {
            return;
        }

        $this->withQuery($model, 'whereIn', [$value]);
    }

    /**
     * Render this filter.
     *
     * @return string
     */
    public function render()
    {
        return $this->renderCheckbox();
    }
}
