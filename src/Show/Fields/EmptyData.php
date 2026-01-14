<?php

namespace Dcat\Admin\Show\Fields;

use Dcat\Admin\Show\AbstractField;

/**
 * 空数据 Show 字段
 *
 * 用于在详情页处理空值，
 * 当值为空时显示占位符。
 */
class EmptyData extends AbstractField
{
    /**
     * 占位符.
     */
    protected string $placeholder = '-';

    /**
     * 设置占位符.
     *
     * @param  string  $placeholder
     * @return $this
     */
    public function placeholder(string $placeholder): static
    {
        $this->placeholder = $placeholder;

        return $this;
    }

    /**
     * 渲染显示.
     *
     * @return mixed
     */
    public function render(): mixed
    {
        return $this->value ?: $this->placeholder;
    }
}
