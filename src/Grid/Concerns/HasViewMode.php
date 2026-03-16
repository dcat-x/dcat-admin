<?php

declare(strict_types=1);

namespace Dcat\Admin\Grid\Concerns;

use Dcat\Admin\Grid\Tools;

trait HasViewMode
{
    const MODE_TABLE = 'table';

    const MODE_CARD = 'card';

    const MODE_LIST = 'list';

    protected $viewModeEnabled = false;

    protected $defaultViewMode = 'table';

    protected $availableViewModes = [];

    public function viewMode(string $default = 'table', array $modes = ['table', 'card']): static
    {
        $this->viewModeEnabled = true;
        $this->defaultViewMode = $default;
        $this->availableViewModes = $modes;

        return $this;
    }

    public function allowViewMode(): bool
    {
        return $this->viewModeEnabled;
    }

    public function getAvailableViewModes(): array
    {
        return $this->availableViewModes;
    }

    public function getCurrentViewMode(): string
    {
        if (! $this->viewModeEnabled) {
            return self::MODE_TABLE;
        }

        $mode = request('_view_', $this->defaultViewMode);

        return in_array($mode, $this->availableViewModes) ? $mode : $this->defaultViewMode;
    }

    public function renderViewModeButton(): string
    {
        if (! $this->allowViewMode()) {
            return '';
        }

        return (new Tools\ViewModeButton($this))->render();
    }

    protected function applyViewMode(): void
    {
        if (! $this->viewModeEnabled) {
            return;
        }

        $mode = $this->getCurrentViewMode();

        if ($mode === self::MODE_TABLE) {
            return;
        }

        $this->view = 'admin::grid.'.$mode;
    }
}
