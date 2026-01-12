<?php

namespace Dcat\Admin\Form\Concerns;

use Closure;
use Dcat\Admin\Form\Row;

trait HasRows
{
    /**
     * Field rows in form.
     *
     * @var Row[]
     */
    protected $rows = [];

    /**
     * Add a row in form.
     *
     * @return $this
     */
    public function row(Closure $callback)
    {
        $this->rows[] = new Row($callback, $this);

        return $this;
    }

    /**
     * @return Row[]
     */
    public function rows()
    {
        return $this->rows;
    }
}
