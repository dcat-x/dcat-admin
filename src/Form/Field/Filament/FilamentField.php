<?php

namespace Dcat\Admin\Form\Field\Filament;

use Dcat\Admin\Admin;
use Dcat\Admin\Form\Field;
use Illuminate\Support\Str;

abstract class FilamentField extends Field
{
    /**
     * Livewire component name.
     */
    protected string $livewireComponent = '';

    /**
     * Filament component configuration.
     */
    protected array $filamentConfig = [];

    /**
     * View for field to render.
     */
    protected $view = 'admin::form.filament.base';

    /**
     * Get the Livewire component name.
     */
    public function getLivewireComponent(): string
    {
        return $this->livewireComponent;
    }

    /**
     * Generate a unique wire:key for this field instance.
     */
    public function getWireKey(): string
    {
        $column = is_array($this->column) ? implode('_', $this->column) : $this->column;

        return 'filament_'.Str::slug($column).'_'.Str::random(8);
    }

    /**
     * Set Filament component configuration.
     */
    public function filamentConfig(array $config): static
    {
        $this->filamentConfig = array_merge($this->filamentConfig, $config);

        return $this;
    }

    /**
     * Get Filament component configuration.
     */
    public function getFilamentConfig(): array
    {
        return $this->filamentConfig;
    }

    /**
     * Require Filament assets.
     */
    public static function requireAssets(): void
    {
        parent::requireAssets();

        Admin::requireAssets('@filament-forms');
    }

    /**
     * Get the view variables of this field.
     */
    public function defaultVariables(): array
    {
        return array_merge(parent::defaultVariables(), [
            'livewireComponent' => $this->getLivewireComponent(),
            'wireKey' => $this->getWireKey(),
            'filamentConfig' => $this->getFilamentConfig(),
        ]);
    }
}
