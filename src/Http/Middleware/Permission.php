<?php

namespace Dcat\Admin\Http\Middleware;

use Dcat\Admin\Admin;
use Dcat\Admin\Exception\RuntimeException;
use Dcat\Admin\Http\Auth\Permission as Checker;
use Dcat\Admin\Support\Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class Permission
{
    /**
     * @var string
     */
    protected $middlewarePrefix = 'admin.permission:';

    /**
     * Handle an incoming request.
     *
     * @param  array  $args
     * @return mixed
     */
    public function handle(Request $request, \Closure $next, ...$args)
    {
        $user = Admin::user();

        if (
            ! $user
            || ! empty($args)
            || ! config('admin.permission.enable')
            || $this->shouldPassThrough($request)
            || $user->isAdministrator()
            || $this->checkRoutePermission($request)
        ) {
            return $next($request);
        }

        // 检查基于权限 http_path 的访问控制
        if ($user->allPermissions()->first(function ($permission) use ($request) {
            return $permission->shouldPassThrough($request);
        })) {
            return $next($request);
        }

        // 检查基于角色绑定菜单的访问控制
        if (config('admin.menu.role_bind_menu', true) && $this->checkMenuPermission($request, $user)) {
            return $next($request);
        }

        Checker::error();

        return $next($request);
    }

    /**
     * 检查基于菜单绑定角色的权限
     */
    protected function checkMenuPermission(Request $request, $user): bool
    {
        $path = '/'.trim($request->path(), '/');
        $prefix = config('admin.route.prefix', 'admin');

        // 移除前缀
        if ($prefix && strpos($path, '/'.$prefix) === 0) {
            $path = substr($path, strlen($prefix) + 1);
        }

        $path = ltrim($path, '/');

        // 移除路径中的 ID 部分 (如 users/1/edit -> users/*/edit)
        $pathPattern = preg_replace('/\/\d+/', '/*', $path);

        $menuModel = config('admin.database.menu_model');
        $menu = $menuModel::with('roles')->where(function ($query) use ($path, $pathPattern) {
            $query->where('uri', $path)
                ->orWhere('uri', $pathPattern)
                ->orWhere('uri', 'like', $path.'%');
        })->first();

        if (! $menu) {
            return false;
        }

        $menuRoles = $menu->roles->pluck('slug')->toArray();

        // 如果菜单没有绑定角色，普通用户不允许访问
        if (empty($menuRoles)) {
            return false;
        }

        // 检查用户角色是否在菜单绑定的角色中
        return $user->inRoles($menuRoles);
    }

    /**
     * If the route of current request contains a middleware prefixed with 'admin.permission:',
     * then it has a manually set permission middleware, we need to handle it first.
     *
     * @return bool
     */
    public function checkRoutePermission(Request $request)
    {
        if (! $middleware = collect($request->route()->middleware())->first(function ($middleware) {
            return Str::startsWith($middleware, $this->middlewarePrefix);
        })) {
            return false;
        }

        $args = explode(',', str_replace($this->middlewarePrefix, '', $middleware));

        $method = array_shift($args);

        if (! method_exists(Checker::class, $method)) {
            throw new RuntimeException("Invalid permission method [$method].");
        }

        call_user_func_array([Checker::class, $method], [$args]);

        return true;
    }

    /**
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function isApiRoute($request)
    {
        return $request->routeIs(admin_api_route_name('*'));
    }

    /**
     * Determine if the request has a URI that should pass through verification.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    public function shouldPassThrough($request)
    {
        if ($this->isApiRoute($request) || Authenticate::shouldPassThrough($request)) {
            return true;
        }

        $excepts = array_merge(
            (array) config('admin.permission.except', []),
            Admin::context()->getArray('permission.except')
        );

        foreach ($excepts as $except) {
            if ($request->routeIs($except) || $request->routeIs(admin_route_name($except))) {
                return true;
            }

            $except = admin_base_path($except);

            if ($except !== '/') {
                $except = trim($except, '/');
            }

            if (Helper::matchRequestPath($except)) {
                return true;
            }
        }

        return false;
    }
}
