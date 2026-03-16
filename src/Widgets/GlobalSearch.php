<?php

declare(strict_types=1);

namespace Dcat\Admin\Widgets;

use Dcat\Admin\Support\GlobalSearch\SearchProviderInterface;
use Illuminate\Contracts\Support\Renderable;

class GlobalSearch implements Renderable
{
    /**
     * @var SearchProviderInterface[]
     */
    protected $providers = [];

    /**
     * @var string
     */
    protected $shortcut = 'Ctrl+K';

    public function provider(SearchProviderInterface $provider): static
    {
        $this->providers[] = $provider;

        return $this;
    }

    public function shortcut(string $shortcut): static
    {
        $this->shortcut = $shortcut;

        return $this;
    }

    /**
     * @return SearchProviderInterface[]
     */
    public function getProviders(): array
    {
        return $this->providers;
    }

    public function render()
    {
        if (empty($this->providers)) {
            return '';
        }

        return view('admin::widgets.global-search', [
            'shortcut' => $this->shortcut,
        ])->render();
    }
}
