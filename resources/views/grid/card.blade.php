<div class="dcat-box">
    <div class="d-block pb-0">
        @include('admin::grid.table-toolbar')
    </div>

    {!! $grid->renderFilter() !!}
    {!! $grid->renderHeader() !!}

    <div class="row" style="padding: 0 8px;">
        @foreach($grid->rows() as $row)
        <div class="col-lg-3 col-md-4 col-sm-6 col-12" style="padding: 8px;">
            <div class="card" style="margin-bottom: 0; height: 100%;">
                <div class="card-body" style="padding: 1rem;">
                    @php
                        $columns = $grid->getVisibleColumnNames();
                        $first = true;
                    @endphp
                    @foreach($columns as $name)
                        @if($first)
                            <h5 class="card-title" style="margin-bottom: 0.75rem; font-weight: 600;">
                                {!! $row->column($name) !!}
                            </h5>
                            @php $first = false; @endphp
                        @else
                            <div class="d-flex justify-content-between align-items-center" style="padding: 4px 0; border-bottom: 1px solid #f0f0f0;">
                                <span class="text-muted" style="font-size: 0.85rem;">
                                    @php
                                        $col = $grid->columns()->first(fn($c) => $c->getName() === $name);
                                    @endphp
                                    {{ $col ? $col->getLabel() : $name }}
                                </span>
                                <span style="font-size: 0.85rem;">{!! $row->column($name) !!}</span>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
        @endforeach
        @if($grid->rows()->isEmpty())
        <div class="col-12">
            <div style="margin:5px 0 0 10px;">
                <span class="help-block" style="margin-bottom:0">
                    <i class="feather icon-alert-circle"></i>&nbsp;{{ trans('admin.no_data') }}
                </span>
            </div>
        </div>
        @endif
    </div>

    {!! $grid->renderFooter() !!}
    {!! $grid->renderPagination() !!}
</div>
