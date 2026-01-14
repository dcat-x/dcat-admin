<?php

namespace Dcat\Admin\Grid\Displayers;

/**
 * 货币金额显示器
 *
 * 用于在 Grid 列表中显示以"分"为单位存储的金额，
 * 自动转换为带美元符号的格式化金额。
 */
class Fee extends AbstractDisplayer
{
    /**
     * 显示格式化的金额.
     */
    public function display(string $symbol = '$', int $decimals = 2): string
    {
        return $symbol.money_formatter($this->value, $decimals);
    }
}
