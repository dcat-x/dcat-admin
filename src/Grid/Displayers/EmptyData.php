<?php

namespace Dcat\Admin\Grid\Displayers;

/**
 * 空数据显示器
 *
 * 用于在 Grid 列表中处理空值，
 * 当值为空时显示占位符。
 */
class EmptyData extends AbstractDisplayer
{
    /**
     * 显示数据，空值时显示占位符.
     *
     * @param  string  $placeholder  占位符，默认为 -
     * @return mixed
     */
    public function display(string $placeholder = '-'): mixed
    {
        return $this->value ?: $placeholder;
    }
}
