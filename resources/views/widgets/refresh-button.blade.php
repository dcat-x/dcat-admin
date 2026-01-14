<li class="nav-item">
    <a href="javascript:void(0);" class="container-refresh" @if($tooltip) title="{{ $tooltip }}" @endif>
        <i class="{{ $icon }}"></i>
    </a>
</li>
<script>
    $('.container-refresh').off('click').on('click', function () {
        Dcat.reload();
        Dcat.success('{{ __('admin.refresh_succeeded') }}', null, {
            timeOut: 3000,
        });
    });
</script>
