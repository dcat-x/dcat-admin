<?php

namespace Dcat\Admin\Http\Middleware;

use Dcat\Admin\Admin;
use Dcat\Admin\Exception\RuntimeException;
use Dcat\Admin\Http\Auth\Permission as Checker;
use Dcat\Admin\Models\Administrator;
use Dcat\Admin\Support\Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;

class Permission
{
    /**
     * @var string
     */
    protected $middlewarePrefix = 'admin.permission:';

    /**
     * 菜单匹配缓存（请求级）
     *
     * @var array<string, mixed>
     */
    protected static $menuPermissionCache = [];

    /**
     * 菜单前缀匹配候选缓存（请求级，按首段分组）
     *
     * @var array<string, array<int, mixed>>
     */
    protected static $menuPrefixCandidates = [];

    /**
     * 菜单候选全集缓存（请求级）
     *
     * @var array<int, mixed>|null
     */
    protected static $allMenuCandidates;

    /**
     * 菜单候选按首段索引（请求级）
     *
     * @var array<string, array<int, mixed>>|null
     */
    protected static $menuCandidatesBySegment;

    /**
     * 菜单候选按URI索引（请求级）
     *
     * @var array<string, mixed>|null
     */
    protected static $menuCandidatesByUri;

    /**
     * 缓存所属的请求哈希，用于检测跨请求
     */
    protected static $cacheRequestHash;

    /**
     * Handle an incoming request.
     *
     * @param  array  $args
     * @return mixed
     */
    public function handle(Request $request, \Closure $next, ...$args)
    {
        /** @var Administrator|null $user */
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
    }

    /**
     * 检查基于菜单绑定角色的权限
     */
    protected function checkMenuPermission(Request $request, $user): bool
    {
        [$path, $pathPattern] = $this->normalizeMenuPath($request);

        $menu = $this->findMatchedMenu($path, $pathPattern);

        // 如果没有对应的菜单，允许访问（可能是 API 接口或异步请求）
        if (! $menu) {
            return true;
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
     * 规范化菜单匹配路径
     *
     * @return array{0: string, 1: string}
     */
    protected function normalizeMenuPath(Request $request): array
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

        return [$path, $pathPattern];
    }

    /**
     * 查找匹配菜单（请求级缓存）
     */
    protected function findMatchedMenu(string $path, string $pathPattern)
    {
        $this->refreshRequestCacheIfNeeded();

        $cacheKey = $path.'|'.$pathPattern;

        if (array_key_exists($cacheKey, static::$menuPermissionCache)) {
            return static::$menuPermissionCache[$cacheKey];
        }

        $menuByUri = $this->getMenuCandidatesByUri();
        if (isset($menuByUri[$path])) {
            return static::$menuPermissionCache[$cacheKey] = $menuByUri[$path];
        }
        if ($pathPattern !== $path && isset($menuByUri[$pathPattern])) {
            return static::$menuPermissionCache[$cacheKey] = $menuByUri[$pathPattern];
        }

        $menu = null;
        $matched = null;
        $matchedUriLength = -1;

        foreach ($this->getPrefixCandidates($path) as $candidate) {
            $uri = trim((string) ($candidate->uri ?? ''), '/');

            if ($uri === '') {
                continue;
            }

            // 优先精确匹配或通配匹配
            if ($uri === $path || $uri === $pathPattern) {
                $menu = $candidate;
                break;
            }

            // 再执行前缀匹配，取最长前缀
            if (! $this->matchesPathPrefix($path, $uri)) {
                continue;
            }

            $uriLength = strlen($uri);
            if ($uriLength > $matchedUriLength) {
                $matched = $candidate;
                $matchedUriLength = $uriLength;
            }
        }

        $menu = $menu ?: $matched;

        return static::$menuPermissionCache[$cacheKey] = $menu;
    }

    protected function matchesPathPrefix(string $path, string $uri): bool
    {
        if (! str_starts_with($path, $uri)) {
            return false;
        }

        $uriLength = strlen($uri);
        $pathLength = strlen($path);

        if ($pathLength === $uriLength) {
            return true;
        }

        return $path[$uriLength] === '/';
    }

    /**
     * @return iterable<mixed>
     */
    protected function getPrefixCandidates(string $path): iterable
    {
        $segment = strtok($path, '/');
        $cacheKey = $segment ?: '*';

        if (array_key_exists($cacheKey, static::$menuPrefixCandidates)) {
            return static::$menuPrefixCandidates[$cacheKey];
        }

        $allCandidates = $this->getAllMenuCandidates();

        if (! $segment) {
            return static::$menuPrefixCandidates[$cacheKey] = $allCandidates;
        }

        $indexed = $this->getMenuCandidatesBySegment();

        return static::$menuPrefixCandidates[$cacheKey] = $indexed[$segment] ?? [];
    }

    protected function refreshRequestCacheIfNeeded(): void
    {
        $requestHash = spl_object_id(App::make('request'));

        if (static::$cacheRequestHash === $requestHash) {
            return;
        }

        static::$cacheRequestHash = $requestHash;
        static::$menuPermissionCache = [];
        static::$menuPrefixCandidates = [];
        static::$allMenuCandidates = null;
        static::$menuCandidatesBySegment = null;
        static::$menuCandidatesByUri = null;
    }

    /**
     * @return array<int, mixed>
     */
    protected function getAllMenuCandidates(): array
    {
        if (static::$allMenuCandidates !== null) {
            return static::$allMenuCandidates;
        }

        $menuModel = config('admin.database.menu_model');

        return static::$allMenuCandidates = $menuModel::with('roles')
            ->where('uri', '!=', '')
            ->get()
            ->all();
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    protected function getMenuCandidatesBySegment(): array
    {
        if (static::$menuCandidatesBySegment !== null) {
            return static::$menuCandidatesBySegment;
        }

        $indexed = [];
        foreach ($this->getAllMenuCandidates() as $candidate) {
            $uri = trim((string) ($candidate->uri ?? ''), '/');
            if ($uri === '') {
                continue;
            }

            $segment = strtok($uri, '/');
            if (! $segment) {
                continue;
            }

            $indexed[$segment][] = $candidate;
        }

        return static::$menuCandidatesBySegment = $indexed;
    }

    /**
     * @return array<string, mixed>
     */
    protected function getMenuCandidatesByUri(): array
    {
        if (static::$menuCandidatesByUri !== null) {
            return static::$menuCandidatesByUri;
        }

        $indexed = [];
        foreach ($this->getAllMenuCandidates() as $candidate) {
            $uri = trim((string) ($candidate->uri ?? ''), '/');
            if ($uri === '') {
                continue;
            }

            // 保留首个匹配，避免重复 URI 时后写覆盖
            if (! isset($indexed[$uri])) {
                $indexed[$uri] = $candidate;
            }
        }

        return static::$menuCandidatesByUri = $indexed;
    }

    /**
     * If the route of current request contains a middleware prefixed with 'admin.permission:',
     * then it has a manually set permission middleware, we need to handle it first.
     *
     * @return bool
     */
    public function checkRoutePermission(Request $request)
    {
        $route = $request->route();
        if (! $route) {
            return false;
        }

        $middleware = null;
        foreach ($route->middleware() as $item) {
            if (Str::startsWith($item, $this->middlewarePrefix)) {
                $middleware = $item;
                break;
            }
        }

        if (! $middleware) {
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
        $handled = [];

        foreach ($excepts as $except) {
            if ($except === null || $except === '') {
                continue;
            }

            $except = (string) $except;

            if (isset($handled[$except])) {
                continue;
            }
            $handled[$except] = true;

            if ($request->routeIs($except)) {
                return true;
            }

            $adminRoute = admin_route_name($except);
            if ($adminRoute !== $except && $request->routeIs($adminRoute)) {
                return true;
            }

            $pathExcept = admin_base_path($except);

            if ($pathExcept !== '/') {
                $pathExcept = trim($pathExcept, '/');
            }

            if (Helper::matchRequestPath($pathExcept)) {
                return true;
            }
        }

        return false;
    }
}
