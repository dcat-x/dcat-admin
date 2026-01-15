<div class="{{$viewClass['form-group']}}">

    <label class="{{$viewClass['label']}} control-label">{!! $label !!}</label>

    <div class="{{$viewClass['field']}}">

        @include('admin::form.error')

        {{-- Hidden input for form submission --}}
        <input type="hidden"
               name="{{$name}}"
               id="{{$id}}_hidden"
               value="{{ is_array($value) ? json_encode($value) : $value }}" />

        {{-- Filament Livewire component container --}}
        <div class="filament-scope">
            @livewire($livewireComponent, [
                'fieldName' => $name,
                'fieldValue' => $value,
                'fieldConfig' => $filamentConfig,
                'fieldDisabled' => $disabled ?? false,
            ], key($wireKey))
        </div>

        @include('admin::form.help-block')

    </div>
</div>

<script>
    // Sync Livewire component value to hidden input
    document.addEventListener('livewire:initialized', function() {
        Livewire.on('filament-field-updated', function(data) {
            if (data.name === '{{ $name }}') {
                var hiddenInput = document.getElementById('{{ $id }}_hidden');
                if (hiddenInput) {
                    hiddenInput.value = typeof data.value === 'object'
                        ? JSON.stringify(data.value)
                        : data.value;
                }
            }
        });
    });
</script>
