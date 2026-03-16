<?php

declare(strict_types=1);

namespace Dcat\Admin\Grid\Tools;

use Dcat\Admin\Grid;
use Illuminate\Contracts\Support\Renderable;

class ViewModeButton implements Renderable
{
    protected $grid;

    protected $icons = [
        'table' => 'feather icon-align-justify',
        'card' => 'feather icon-grid',
        'list' => 'feather icon-menu',
    ];

    public function __construct(Grid $grid)
    {
        $this->grid = $grid;
    }

    protected function buildUrl(string $mode): string
    {
        $input = array_merge(request()->all(), ['_view_' => $mode]);

        unset($input['_pjax']);

        return $this->grid->resource().'?'.http_build_query($input);
    }

    public function render()
    {
        $current = $this->grid->getCurrentViewMode();
        $modes = $this->grid->getAvailableViewModes();

        $buttons = '';
        foreach ($modes as $mode) {
            $icon = $this->icons[$mode] ?? 'feather icon-circle';
            $class = $mode === $current ? 'btn btn-sm btn-primary' : 'btn btn-sm btn-default';
            $url = $this->buildUrl($mode);
            $buttons .= "<a href=\"{$url}\" class=\"{$class}\"><i class=\"{$icon}\"></i></a>";
        }

        return $this->grid->tools()->format(
            "<div class=\"btn-group\" style=\"margin-right:3px\">{$buttons}</div>"
        );
    }
}
