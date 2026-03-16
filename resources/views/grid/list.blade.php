<div class="dcat-box">
    <div class="d-block pb-0">
        @include('admin::grid.table-toolbar')
    </div>

    {!! $grid->renderFilter() !!}
    {!! $grid->renderHeader() !!}

    <div class="list-group" style="padding: 0 16px 16px;">
        @foreach($grid->rows() as $row)
        <div class="list-group-item" style="padding: 12px 16px;">
            <div class="d-flex align-items-center flex-wrap" style="gap: 16px;">
                @foreach($grid->getVisibleColumnNames() as $name)
                <div>
                    <small class="text-muted d-block">
                        @php $col = $grid->columns()->first(fn($c) => $c->getName() === $name); @endphp
                        {{ $col ? $col->getLabel() : $name }}
                    </small>
                    <span>{!! $row->column($name) !!}</span>
                </div>
                @endforeach
            </div>
        </div>
        @endforeach
        @if($grid->rows()->isEmpty())
        <div style="margin:5px 0 0 10px;">
            <span class="help-block" style="margin-bottom:0">
                <i class="feather icon-alert-circle"></i>&nbsp;{{ trans('admin.no_data') }}
            </span>
        </div>
        @endif
    </div>

    {!! $grid->renderFooter() !!}
    {!! $grid->renderPagination() !!}
</div>
