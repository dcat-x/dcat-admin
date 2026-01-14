<div class="{{ $configData['horizontal_menu'] ? 'header-navbar navbar-expand-sm navbar navbar-horizontal' : 'main-menu' }}">
    <div class="main-menu-content">
        <aside class="{{ $configData['horizontal_menu'] ? 'main-horizontal-sidebar' : 'main-sidebar' }} {{ $configData['sidebar_style'] }}">

            @if(! $configData['horizontal_menu'])
            <div class="navbar-header">
                <ul class="nav navbar-nav flex-row">
                    <li class="nav-item mr-auto">
                        <a href="{{ admin_url('/') }}" class="navbar-brand waves-effect waves-light">
                            <div style="font-size: 18px;height: 50px;line-height: 50px;">{!! config('admin.logo') !!}</div>
                        </a>
                    </li>
                </ul>
            </div>
            @endif

            <div class="p-0 {{ $configData['horizontal_menu'] ? 'pl-1 pr-1' : 'sidebar pb-3' }}">
                <ul class="nav nav-pills nav-sidebar {{ $configData['horizontal_menu'] ? '' : 'flex-column' }}"
                    {!! $configData['horizontal_menu'] ? '' : 'data-widget="treeview"' !!}
                     style="padding-top: 10px">
                    {!! admin_section(Dcat\Admin\Admin::SECTION['LEFT_SIDEBAR_MENU_TOP']) !!}

                    {!! admin_section(Dcat\Admin\Admin::SECTION['LEFT_SIDEBAR_MENU']) !!}

                    {!! admin_section(Dcat\Admin\Admin::SECTION['LEFT_SIDEBAR_MENU_BOTTOM']) !!}
                </ul>
            </div>
        </aside>
    </div>
</div>