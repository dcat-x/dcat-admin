@if($user)
<li class="dropdown dropdown-user nav-item">
    <a class="dropdown-toggle nav-link dropdown-user-link" href="#" data-toggle="dropdown">
        <div class="user-nav d-sm-flex">
            <span class="user-name text-bold-600">{{ $user->name }}</span>&nbsp;
            <span class="fa fa-angle-down"></span>
        </div>
    </a>
    <div class="dropdown-menu dropdown-menu-right">
        <a href="{{ admin_url('auth/setting') }}" class="dropdown-item">
            <i class="feather icon-user"></i> {{ trans('admin.setting') }}
        </a>

        <div class="dropdown-divider"></div>

        <a class="dropdown-item" href="{{ admin_url('auth/logout') }}">
            <i class="feather icon-power"></i> {{ trans('admin.logout') }}
        </a>
    </div>
</li>
@endif