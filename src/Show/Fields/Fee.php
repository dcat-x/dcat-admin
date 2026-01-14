<?php

namespace Dcat\Admin\Show\Fields;

use Dcat\Admin\Show\AbstractField;

/**
 * 货币金额 Show 字段
 *
 * 用于在详情页显示以"分"为单位存储的金额，
 * 自动转换为带美元符号的格式化金额。
 */
class Fee extends AbstractField
{
    /**
     * 货币符号.
     */
    protected string $symbol = '$';

    /**
     * 小数位数.
     */
    protected int $decimals = 2;

    /**
     * 设置货币符号.
     *
     * @param  string  $symbol
     * @return $this
     */
    public function symbol(string $symbol): static
    {
        $this->symbol = $symbol;

        return $this;
    }

    /**
     * 设置小数位数.
     *
     * @param  int  $decimals
     * @return $this
     */
    public function decimals(int $decimals): static
    {
        $this->decimals = $decimals;

        return $this;
    }

    /**
     * 渲染显示.
     *
     * @return string
     */
    public function render(): string
    {
        return $this->symbol.money_formatter($this->value, $this->decimals);
    }
}
