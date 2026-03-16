<?php

namespace Dcat\Admin\Support\GlobalSearch;

use Dcat\Admin\Models\Menu;

class MenuSearchProvider implements SearchProviderInterface
{
    public function title(): string
    {
        return admin_trans('menu');
    }

    public function search(string $keyword, int $limit = 5): array
    {
        return Menu::query()
            ->where('title', 'like', "%{$keyword}%")
            ->where('uri', '!=', '')
            ->limit($limit)
            ->get()
            ->map(function ($menu) {
                return [
                    'title' => $menu->title,
                    'url' => admin_url($menu->uri),
                    'icon' => $menu->icon ?: 'feather icon-circle',
                ];
            })
            ->toArray();
    }
}
