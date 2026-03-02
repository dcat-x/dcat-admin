<div class="input-group input-group-sm">
    <div class="input-group-prepend">
        <span class="input-group-text bg-white"><b>{!! $label !!}</b></span>
    </div>

    <div id="{{ $id }}" class="form-control" style="display: flex; align-items: center; min-height: 30px; padding: 0;">
        <span class="batch-input-wrap" style="flex: 1; display: flex;">
            <input type="text" id="input-{{ $id }}" class="border-0" style="flex: 1; outline: none; padding: 0 8px; font-size: 13px; min-width: 0;" placeholder="{{ $placeholder }}" />
        </span>
        <span class="batch-input-tag-wrap d-none" style="flex: 1;"></span>
    </div>

    <input name="{{ $name }}" type="hidden" id="hidden-{{ $id }}" value="{{ implode(',', \Dcat\Admin\Support\Helper::array($value)) }}" />
    <div class="input-group-append">
        <div id="batch-btn-{{ $id }}" class="btn btn-primary btn-sm" style="cursor: pointer;">
            &nbsp;<i class="feather icon-list"></i>&nbsp;{{ $batchButtonText }}&nbsp;
        </div>
    </div>
</div>
