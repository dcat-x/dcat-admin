<?php

namespace Dcat\Admin\Livewire;

use Filament\Forms\Components\RichEditor;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Schemas\Schema;
use Livewire\Component;

class RichEditorComponent extends Component implements HasForms
{
    use InteractsWithForms;

    public string $fieldName;

    public ?string $fieldValue = null;

    public array $fieldConfig = [];

    public bool $fieldDisabled = false;

    /**
     * Form data state.
     */
    public ?array $data = [];

    public function mount(
        string $fieldName,
        ?string $fieldValue = null,
        array $fieldConfig = [],
        bool $fieldDisabled = false
    ): void {
        $this->fieldName = $fieldName;
        $this->fieldValue = $fieldValue;
        $this->fieldConfig = $fieldConfig;
        $this->fieldDisabled = $fieldDisabled;

        $this->form->fill([
            'content' => $fieldValue,
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                RichEditor::make('content')
                    ->hiddenLabel()
                    ->disabled($this->fieldDisabled)
                    ->toolbarButtons($this->fieldConfig['toolbarButtons'] ?? [
                        'bold',
                        'italic',
                        'underline',
                        'strike',
                        'link',
                        'orderedList',
                        'bulletList',
                        'h2',
                        'h3',
                        'blockquote',
                        'codeBlock',
                        'attachFiles',
                    ])
                    ->fileAttachmentsDisk($this->fieldConfig['disk'] ?? 'public')
                    ->fileAttachmentsDirectory($this->fieldConfig['directory'] ?? 'filament-attachments')
                    ->fileAttachmentsVisibility($this->fieldConfig['visibility'] ?? 'public'),
            ])
            ->statePath('data');
    }

    public function updatedData(): void
    {
        $this->fieldValue = $this->data['content'] ?? null;

        $this->dispatch('filament-field-updated', [
            'name' => $this->fieldName,
            'value' => $this->fieldValue,
        ]);
    }

    public function render()
    {
        return view('admin::livewire.rich-editor');
    }
}
