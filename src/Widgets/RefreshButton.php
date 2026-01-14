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
     *
     * @var string
     */
    protected $view = 'admin::widgets.refresh-button';

    /**
     * 图标类名.
     *
     * @var string
     */
    protected string $icon = 'feather icon-refresh-cw';

    /**
     * 提示文本.
     *
     * @var string|null
     */
    protected ?string $tooltip = null;

    /**
     * 设置图标.
     *
     * @param  string  $icon
     * @return $this
     */
    public function icon(string $icon): static
    {
        $this->icon = $icon;

        return $this;
    }

    /**
     * 设置提示文本.
     *
     * @param  string  $tooltip
     * @return $this
     */
    public function tooltip(string $tooltip): static
    {
        $this->tooltip = $tooltip;

        return $this;
    }

    /**
     * 获取视图变量.
     *
     * @return array
     */
    public function defaultVariables(): array
    {
        return array_merge(parent::defaultVariables(), [
            'icon' => $this->icon,
            'tooltip' => $this->tooltip,
        ]);
    }
}
