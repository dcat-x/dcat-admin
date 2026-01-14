<div class="filter-input col-sm-{{ $width }}" style="{!! $style !!}">
    <div class="form-group">
        <div class="input-group input-group-sm">
            <div class="input-group-prepend">
                <span class="input-group-text bg-white text-capitalize"><b>{!! $label !!}</b></span>
            </div>
            <div class="toggle-container" style="display: flex; align-items: center; padding: 0 10px;">
                <input type="hidden" name="{{ $name }}" id="{{ $id }}-hidden" value="{{ $value ?? $offValue }}">
                <div class="btn-group btn-group-toggle" data-toggle="buttons">
                    <label class="btn btn-{{ $size ?? 'sm' }} {{ ($value ?? $offValue) == $offValue ? 'btn-white active' : 'btn-outline-secondary' }}" data-value="{{ $offValue }}">
                        <input type="radio" name="{{ $name }}-radio" autocomplete="off" {{ ($value ?? $offValue) == $offValue ? 'checked' : '' }}> {{ $offText }}
                    </label>
                    <label class="btn btn-{{ $size ?? 'sm' }} {{ $value == $onValue ? 'btn-primary active' : 'btn-outline-secondary' }}" data-value="{{ $onValue }}">
                        <input type="radio" name="{{ $name }}-radio" autocomplete="off" {{ $value == $onValue ? 'checked' : '' }}> {{ $onText }}
                    </label>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    var container = $('#{{ $id }}-hidden').closest('.toggle-container');
    var hiddenInput = $('#{{ $id }}-hidden');

    container.find('.btn-group-toggle .btn').on('click', function() {
        var $btn = $(this);
        var value = $btn.data('value');

        hiddenInput.val(value);

        container.find('.btn-group-toggle .btn').removeClass('btn-primary btn-white active').addClass('btn-outline-secondary');
        $btn.removeClass('btn-outline-secondary').addClass(value == '{{ $onValue }}' ? 'btn-primary active' : 'btn-white active');
    });
})();
</script>
