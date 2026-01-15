<?php

namespace Dcat\Admin\Livewire;

use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Http;
use Livewire\Component;

class LiveSelectComponent extends Component implements HasForms
{
    use InteractsWithForms;

    public string $fieldName;

    public mixed $fieldValue = null;

    public array $fieldConfig = [];

    public bool $fieldDisabled = false;

    /**
     * Form data state.
     */
    public ?array $data = [];

    public function mount(
        string $fieldName,
        mixed $fieldValue = null,
        array $fieldConfig = [],
        bool $fieldDisabled = false
    ): void {
        $this->fieldName = $fieldName;
        $this->fieldValue = $fieldValue;
        $this->fieldConfig = $fieldConfig;
        $this->fieldDisabled = $fieldDisabled;

        $this->form->fill([
            'value' => $fieldValue,
        ]);
    }

    public function form(Schema $schema): Schema
    {
        $select = Select::make('value')
            ->hiddenLabel()
            ->disabled($this->fieldDisabled)
            ->searchable()
            ->preload($this->fieldConfig['preload'] ?? false);

        // Static options
        if (isset($this->fieldConfig['options'])) {
            $select->options($this->fieldConfig['options']);
        }

        // Remote search
        if (isset($this->fieldConfig['searchUrl'])) {
            $searchUrl = $this->fieldConfig['searchUrl'];
            $valueField = $this->fieldConfig['valueField'] ?? 'id';
            $labelField = $this->fieldConfig['labelField'] ?? 'name';
            $minChars = $this->fieldConfig['minChars'] ?? 1;

            $select->getSearchResultsUsing(function (string $search) use ($searchUrl, $valueField, $labelField, $minChars) {
                if (strlen($search) < $minChars) {
                    return [];
                }

                $response = Http::get($searchUrl, ['q' => $search]);

                if ($response->successful()) {
                    $data = $response->json('data', $response->json());

                    return collect($data)->pluck($labelField, $valueField)->toArray();
                }

                return [];
            });

            // Load selected option label
            $select->getOptionLabelUsing(function ($value) use ($searchUrl, $valueField, $labelField) {
                if (empty($value)) {
                    return null;
                }

                $response = Http::get($searchUrl, [$valueField => $value]);

                if ($response->successful()) {
                    $data = $response->json('data', $response->json());
                    $item = is_array($data) ? ($data[0] ?? null) : $data;

                    return $item[$labelField] ?? null;
                }

                return null;
            });
        }

        // Multiple selection
        if ($this->fieldConfig['multiple'] ?? false) {
            $select->multiple();
        }

        // Placeholder
        if (isset($this->fieldConfig['placeholder'])) {
            $select->placeholder($this->fieldConfig['placeholder']);
        }

        return $schema
            ->components([$select])
            ->statePath('data');
    }

    public function updatedData(): void
    {
        $this->fieldValue = $this->data['value'] ?? null;

        $this->dispatch('filament-field-updated', [
            'name' => $this->fieldName,
            'value' => $this->fieldValue,
        ]);
    }

    public function render()
    {
        return view('admin::livewire.live-select');
    }
}
