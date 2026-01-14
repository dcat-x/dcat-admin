<div class="form-group">
    <div class="input-group input-group-sm">
        <div class="input-group-prepend">
            <span class="input-group-text bg-white text-capitalize"><b>{!! $label !!}</b></span>
        </div>

        @foreach($ranges as $rangeLabel => $range)
            <button type="button"
                    class="btn btn-sm btn-white date-range-quick-btn"
                    data-start="{{ $range[0] }}"
                    data-end="{{ $range[1] }}"
                    data-start-input="#{{ $id['start'] }}"
                    data-end-input="#{{ $id['end'] }}">
                {{ $rangeLabel }}
            </button>
        @endforeach
    </div>

    @if($showDateInputs)
    <div class="input-group input-group-sm mt-1">
        <div class="input-group-prepend">
            <span class="input-group-text bg-white"><i class="feather icon-calendar"></i></span>
        </div>
        <input autocomplete="off" type="text" class="form-control" id="{{$id['start']}}" placeholder="{{ trans('admin.start') }}" name="{{$name['start']}}" value="{{ request($name['start'], \Illuminate\Support\Arr::get($value, 'start')) }}">
        <span class="input-group-addon" style="border-left: 0; border-right: 0;">-</span>
        <input autocomplete="off" type="text" class="form-control" id="{{$id['end']}}" placeholder="{{ trans('admin.end') }}" name="{{$name['end']}}" value="{{ request($name['end'], \Illuminate\Support\Arr::get($value, 'end')) }}">
    </div>
    @else
        <input type="hidden" id="{{$id['start']}}" name="{{$name['start']}}" value="{{ request($name['start'], \Illuminate\Support\Arr::get($value, 'start')) }}">
        <input type="hidden" id="{{$id['end']}}" name="{{$name['end']}}" value="{{ request($name['end'], \Illuminate\Support\Arr::get($value, 'end')) }}">
    @endif
</div>

<script require="@moment,@bootstrap-datetimepicker">
    (function () {
        var options = {!! admin_javascript_json($dateOptions) !!};

        @if($showDateInputs)
        $('#{{ $id['start'] }}').datetimepicker(options);
        $('#{{ $id['end'] }}').datetimepicker($.extend({}, options, {useCurrent: false}));
        $("#{{ $id['start'] }}").on("dp.change", function (e) {
            $('#{{ $id['end'] }}').data("DateTimePicker").minDate(e.date);
        });
        $("#{{ $id['end'] }}").on("dp.change", function (e) {
            $('#{{ $id['start'] }}').data("DateTimePicker").maxDate(e.date);
        });
        @endif

        $('.date-range-quick-btn').on('click', function() {
            var $btn = $(this);
            var startInput = $($btn.data('start-input'));
            var endInput = $($btn.data('end-input'));

            startInput.val($btn.data('start'));
            endInput.val($btn.data('end'));

            @if($showDateInputs)
            if (startInput.data('DateTimePicker')) {
                startInput.data('DateTimePicker').date(moment($btn.data('start')));
            }
            if (endInput.data('DateTimePicker')) {
                endInput.data('DateTimePicker').date(moment($btn.data('end')));
            }
            @endif

            $('.date-range-quick-btn').removeClass('btn-primary').addClass('btn-white');
            $btn.removeClass('btn-white').addClass('btn-primary');
        });
    })();
</script>
