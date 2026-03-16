<?php

declare(strict_types=1);

namespace Dcat\Admin\Tree;

use Dcat\Admin\Actions\Action;
use Illuminate\Database\Eloquent\Model;

class RowAction extends Action
{
    /**
     * @var \Dcat\Admin\Tree\Actions;
     */
    protected $actions;

    /**
     * @var Model
     */
    protected $row;

    /**
     * 获取主键值.
     *
     * @return array|mixed|string
     */
    public function getKey()
    {
        if ($key = parent::getKey()) {
            return $key;
        }

        return $this->row->{$this->actions->parent()->getKeyName()};
    }

    /**
     * 获取行数据.
     *
     * @return Model
     */
    public function getRow()
    {
        return $this->row;
    }

    /**
     * 获取资源路径.
     *
     * @return string
     */
    public function resource()
    {
        return $this->actions->parent()->resource();
    }

    public function getActions()
    {
        return $this->actions;
    }

    public function setParent(Actions $actions)
    {
        $this->actions = $actions;
    }

    public function setRow($row)
    {
        $this->row = $row;
    }
}
