<?php

namespace Dcat\Admin\Widgets;

/**
 * 刷新按钮小部件
 *
 * 用于在页面上添加刷新按钮，点击后刷新当前页面。
 */
class RefreshButton extends Widget
{
    /**
     * 视图模板.
     */
    protected $view = 'admin::widgets.refresh-button';

    /**
     * 图标类名.
     */
    protected string $icon = 'feather icon-refresh-cw';

    /**
     * 提示文本.
     */
    protected ?string $tooltip = null;

    /**
     * 设置图标.
     */
    public function icon(string $icon): static
    {
        $this->icon = $icon;

        return $this;
    }

    /**
     * 设置提示文本.
     */
    public function tooltip(string $tooltip): static
    {
        $this->tooltip = $tooltip;

        return $this;
    }

    /**
     * 获取视图变量.
     */
    public function defaultVariables(): array
    {
        return array_merge(parent::defaultVariables(), [
            'icon' => $this->icon,
            'tooltip' => $this->tooltip,
        ]);
    }
}
