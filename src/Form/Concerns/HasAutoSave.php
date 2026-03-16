<?php

declare(strict_types=1);

namespace Dcat\Admin\Form\Concerns;

use Dcat\Admin\Admin;

trait HasAutoSave
{
    protected $autoSave = false;

    protected $autoSaveInterval = 30;

    public function autoSave(int $interval = 30): static
    {
        $this->autoSave = true;
        $this->autoSaveInterval = $interval;

        return $this;
    }

    protected function renderAutoSave(): void
    {
        if (! $this->autoSave) {
            return;
        }

        $formId = $this->getElementId();
        $interval = $this->autoSaveInterval;

        Admin::script(<<<JS
new Dcat.FormAutoSave({
    form: '#{$formId}',
    interval: {$interval}
});
JS);
    }
}
