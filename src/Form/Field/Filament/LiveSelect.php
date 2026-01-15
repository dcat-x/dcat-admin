<?php

namespace Dcat\Admin\Form\Field\Filament;

class LiveSelect extends FilamentField
{
    /**
     * Livewire component name.
     */
    protected string $livewireComponent = 'dcat-admin::live-select';

    /**
     * Set the search URL for remote options.
     */
    public function searchUrl(string $url): static
    {
        return $this->filamentConfig(['searchUrl' => $url]);
    }

    /**
     * Set static options.
     */
    public function options($options = [])
    {
        $this->filamentConfig(['options' => $options]);

        return parent::options($options);
    }

    /**
     * Set the value field name for remote data.
     */
    public function valueField(string $field): static
    {
        return $this->filamentConfig(['valueField' => $field]);
    }

    /**
     * Set the label field name for remote data.
     */
    public function labelField(string $field): static
    {
        return $this->filamentConfig(['labelField' => $field]);
    }

    /**
     * Enable multiple selection.
     */
    public function multiple(bool $multiple = true): static
    {
        return $this->filamentConfig(['multiple' => $multiple]);
    }

    /**
     * Set minimum characters before searching.
     */
    public function minChars(int $chars): static
    {
        return $this->filamentConfig(['minChars' => $chars]);
    }

    /**
     * Enable preloading options.
     */
    public function preload(bool $preload = true): static
    {
        return $this->filamentConfig(['preload' => $preload]);
    }

    /**
     * Set placeholder text.
     */
    public function placeholder($placeholder = null): static
    {
        if ($placeholder !== null) {
            $this->filamentConfig(['placeholder' => $placeholder]);
        }

        return parent::placeholder($placeholder);
    }
}
