<?php

namespace Dcat\Admin\Form\Field\Filament;

class RichEditor extends FilamentField
{
    /**
     * Livewire component name.
     */
    protected string $livewireComponent = 'dcat-admin::rich-editor';

    /**
     * Set the storage disk for file attachments.
     */
    public function disk(string $disk): static
    {
        return $this->filamentConfig(['disk' => $disk]);
    }

    /**
     * Set the directory for file attachments.
     */
    public function directory(string $directory): static
    {
        return $this->filamentConfig(['directory' => $directory]);
    }

    /**
     * Set the visibility for file attachments.
     */
    public function visibility(string $visibility): static
    {
        return $this->filamentConfig(['visibility' => $visibility]);
    }

    /**
     * Set the toolbar buttons.
     */
    public function toolbarButtons(array $buttons): static
    {
        return $this->filamentConfig(['toolbarButtons' => $buttons]);
    }

    /**
     * Disable file attachments.
     */
    public function disableAttachments(): static
    {
        $buttons = $this->filamentConfig['toolbarButtons'] ?? [
            'bold', 'italic', 'underline', 'strike', 'link',
            'orderedList', 'bulletList', 'h2', 'h3', 'blockquote', 'codeBlock',
        ];

        return $this->toolbarButtons(array_values(array_filter($buttons, fn ($b) => $b !== 'attachFiles')));
    }
}
