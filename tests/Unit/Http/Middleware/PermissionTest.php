<?php

namespace Dcat\Admin\Tests\Unit\Http\Middleware;

use Dcat\Admin\Admin;
use Dcat\Admin\Http\Middleware\Permission;
use Dcat\Admin\Tests\TestCase;
use Illuminate\Http\Request;
use Mockery;

class FakeMenuPermissionQueryForMiddlewareTest
{
    public static int $exactFirstCalls = 0;

    public static int $fallbackGetCalls = 0;

    public static $exactResult;

    public static $fallbackResult;

    public function where($column, $operator = null, $value = null): self
    {
        if ($column instanceof \Closure) {
            $column($this);
        }

        return $this;
    }

    public function orWhere($column, $operator = null, $value = null): self
    {
        return $this;
    }

    public function first()
    {
        static::$exactFirstCalls++;

        return static::$exactResult;
    }

    public function get()
    {
        static::$fallbackGetCalls++;

        if (static::$fallbackResult === null) {
            return collect();
        }

        return collect(static::$fallbackResult);
    }

    public static function reset(): void
    {
        static::$exactFirstCalls = 0;
        static::$fallbackGetCalls = 0;
        static::$exactResult = null;
        static::$fallbackResult = null;
    }
}

class FakeMenuModelForMiddlewareTest
{
    public static function with($relations): FakeMenuPermissionQueryForMiddlewareTest
    {
        return new FakeMenuPermissionQueryForMiddlewareTest;
    }
}

class TestablePermissionMiddleware extends Permission
{
    public function callNormalizeMenuPath(Request $request): array
    {
        return $this->normalizeMenuPath($request);
    }

    public function callFindMatchedMenu(string $path, string $pathPattern)
    {
        return $this->findMatchedMenu($path, $pathPattern);
    }
}

class PermissionTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->app['config']->set('admin.auth.guard', 'admin');
        $this->app['config']->set('auth.guards.admin', [
            'driver' => 'session',
            'provider' => 'admin',
        ]);
        $this->app['config']->set('auth.providers.admin', [
            'driver' => 'eloquent',
            'model' => \Dcat\Admin\Models\Administrator::class,
        ]);

        FakeMenuPermissionQueryForMiddlewareTest::reset();
        $this->app['config']->set('admin.database.menu_model', FakeMenuModelForMiddlewareTest::class);
    }

    public function test_handle_passes_through_when_no_user(): void
    {
        $middleware = new Permission;
        $request = Request::create('/admin/test');

        $called = false;
        $response = $middleware->handle($request, function ($req) use (&$called) {
            $called = true;

            return 'next';
        });

        // 没有登录用户时，应该直接 pass through
        $this->assertTrue($called);
        $this->assertEquals('next', $response);
    }

    public function test_handle_passes_through_when_permission_disabled(): void
    {
        $this->app['config']->set('admin.permission.enable', false);

        $middleware = new Permission;
        $request = Request::create('/admin/test');

        $called = false;
        $response = $middleware->handle($request, function ($req) use (&$called) {
            $called = true;

            return 'next';
        });

        $this->assertTrue($called);
    }

    public function test_denied_access_does_not_call_next(): void
    {
        $this->app['config']->set('admin.permission.enable', true);
        $this->app['config']->set('admin.permission.except', []);
        $this->app['config']->set('admin.menu.role_bind_menu', false);

        // 创建一个非管理员用户，没有任何权限
        $user = Mockery::mock(\Dcat\Admin\Models\Administrator::class)->makePartial();
        $user->shouldReceive('isAdministrator')->andReturn(false);
        $user->shouldReceive('allPermissions')->andReturn(collect());

        // 注入用户到 Admin
        $guard = Mockery::mock(\Illuminate\Contracts\Auth\StatefulGuard::class);
        $guard->shouldReceive('user')->andReturn($user);
        $guard->shouldReceive('check')->andReturn(true);
        $guard->shouldReceive('id')->andReturn(1);

        \Illuminate\Support\Facades\Auth::shouldReceive('guard')
            ->with('admin')
            ->andReturn($guard);

        $middleware = new Permission;

        $request = Request::create('/admin/test', 'GET');
        $route = new \Illuminate\Routing\Route('GET', 'admin/test', function () {
            return 'test';
        });
        $request->setRouteResolver(function () use ($route) {
            return $route;
        });

        // 注册自定义 error handler 来避免视图渲染（Testbench 环境下视图不完整）
        // Checker::error() 优先调用自定义 handler，如果存在的话
        \Dcat\Admin\Http\Auth\Permission::registerErrorHandler(function () {
            abort(403, 'Permission denied');
        });

        $nextCalled = false;
        $exceptionCaught = false;

        try {
            $middleware->handle($request, function () use (&$nextCalled) {
                $nextCalled = true;

                return 'next';
            });
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
            $exceptionCaught = true;
            $this->assertEquals(403, $e->getStatusCode());
        }

        // 关键断言：$next 不应被调用
        $this->assertFalse($nextCalled, 'Denied access must NOT call $next closure');
        $this->assertTrue($exceptionCaught, 'Checker::error() should throw a 403 exception');
    }

    public function test_handle_passes_through_for_administrator(): void
    {
        $this->app['config']->set('admin.permission.enable', true);

        $user = Mockery::mock(\Dcat\Admin\Models\Administrator::class)->makePartial();
        $user->shouldReceive('isAdministrator')->andReturn(true);

        $guard = Mockery::mock(\Illuminate\Contracts\Auth\StatefulGuard::class);
        $guard->shouldReceive('user')->andReturn($user);
        $guard->shouldReceive('check')->andReturn(true);
        $guard->shouldReceive('id')->andReturn(1);

        \Illuminate\Support\Facades\Auth::shouldReceive('guard')
            ->with('admin')
            ->andReturn($guard);

        $middleware = new Permission;

        $request = Request::create('/admin/test', 'GET');
        $route = new \Illuminate\Routing\Route('GET', 'admin/test', function () {
            return 'test';
        });
        $request->setRouteResolver(function () use ($route) {
            return $route;
        });

        $called = false;
        $response = $middleware->handle($request, function () use (&$called) {
            $called = true;

            return 'next';
        });

        $this->assertTrue($called);
        $this->assertEquals('next', $response);
    }

    public function test_should_pass_through_method_exists(): void
    {
        $middleware = new Permission;
        $this->assertTrue(method_exists($middleware, 'shouldPassThrough'));
    }

    public function test_check_route_permission_method_exists(): void
    {
        $middleware = new Permission;
        $this->assertTrue(method_exists($middleware, 'checkRoutePermission'));
    }

    public function test_normalize_menu_path_removes_prefix_and_generates_pattern(): void
    {
        $this->app['config']->set('admin.route.prefix', 'admin');

        $middleware = new TestablePermissionMiddleware;
        [$path, $pathPattern] = $middleware->callNormalizeMenuPath(Request::create('/admin/users/12/edit', 'GET'));

        $this->assertSame('users/12/edit', $path);
        $this->assertSame('users/*/edit', $pathPattern);
    }

    public function test_find_matched_menu_uses_request_level_cache(): void
    {
        FakeMenuPermissionQueryForMiddlewareTest::$exactResult = (object) ['id' => 66, 'roles' => collect()];
        $this->app->instance('request', Request::create('/admin/orders', 'GET'));

        $middleware = new TestablePermissionMiddleware;

        $first = $middleware->callFindMatchedMenu('orders', 'orders');
        $second = $middleware->callFindMatchedMenu('orders', 'orders');

        $this->assertSame(66, $first->id);
        $this->assertSame(66, $second->id);
        $this->assertSame(1, FakeMenuPermissionQueryForMiddlewareTest::$exactFirstCalls);
    }

    public function test_find_matched_menu_fallback_uses_longest_prefix(): void
    {
        FakeMenuPermissionQueryForMiddlewareTest::$exactResult = null;
        FakeMenuPermissionQueryForMiddlewareTest::$fallbackResult = [
            (object) ['id' => 1, 'uri' => 'orders', 'roles' => collect()],
            (object) ['id' => 2, 'uri' => 'orders/history', 'roles' => collect()],
            (object) ['id' => 3, 'uri' => 'users', 'roles' => collect()],
        ];
        $this->app->instance('request', Request::create('/admin/orders/history/export', 'GET'));

        $middleware = new TestablePermissionMiddleware;
        $matched = $middleware->callFindMatchedMenu('orders/history/export', 'orders/history/export');

        $this->assertNotNull($matched);
        $this->assertSame(2, $matched->id);
        $this->assertSame(1, FakeMenuPermissionQueryForMiddlewareTest::$fallbackGetCalls);
    }

    public function test_handle_allows_when_no_menu_matched(): void
    {
        $this->app['config']->set('admin.permission.enable', true);
        $this->app['config']->set('admin.permission.except', []);
        $this->app['config']->set('admin.menu.role_bind_menu', true);
        FakeMenuPermissionQueryForMiddlewareTest::$exactResult = null;
        FakeMenuPermissionQueryForMiddlewareTest::$fallbackResult = null;

        $user = Mockery::mock(\Dcat\Admin\Models\Administrator::class)->makePartial();
        $user->shouldReceive('isAdministrator')->andReturn(false);
        $user->shouldReceive('allPermissions')->andReturn(collect());
        $user->shouldReceive('inRoles')->never();

        $guard = Mockery::mock(\Illuminate\Contracts\Auth\StatefulGuard::class);
        $guard->shouldReceive('user')->andReturn($user);
        $guard->shouldReceive('check')->andReturn(true);
        $guard->shouldReceive('id')->andReturn(1);

        \Illuminate\Support\Facades\Auth::shouldReceive('guard')
            ->with('admin')
            ->andReturn($guard);

        $middleware = new Permission;
        $request = Request::create('/admin/no-menu-path', 'GET');
        $route = new \Illuminate\Routing\Route('GET', 'admin/no-menu-path', function () {
            return 'test';
        });
        $request->setRouteResolver(fn () => $route);
        $this->app->instance('request', $request);

        $called = false;
        $response = $middleware->handle($request, function () use (&$called) {
            $called = true;

            return 'next';
        });

        $this->assertTrue($called);
        $this->assertSame('next', $response);
    }

    public function test_handle_denies_when_menu_has_no_roles(): void
    {
        $this->app['config']->set('admin.permission.enable', true);
        $this->app['config']->set('admin.permission.except', []);
        $this->app['config']->set('admin.menu.role_bind_menu', true);
        FakeMenuPermissionQueryForMiddlewareTest::$exactResult = (object) [
            'id' => 9,
            'roles' => collect(),
        ];

        $user = Mockery::mock(\Dcat\Admin\Models\Administrator::class)->makePartial();
        $user->shouldReceive('isAdministrator')->andReturn(false);
        $user->shouldReceive('allPermissions')->andReturn(collect());
        $user->shouldReceive('inRoles')->never();

        $guard = Mockery::mock(\Illuminate\Contracts\Auth\StatefulGuard::class);
        $guard->shouldReceive('user')->andReturn($user);
        $guard->shouldReceive('check')->andReturn(true);
        $guard->shouldReceive('id')->andReturn(1);

        \Illuminate\Support\Facades\Auth::shouldReceive('guard')
            ->with('admin')
            ->andReturn($guard);

        \Dcat\Admin\Http\Auth\Permission::registerErrorHandler(function () {
            abort(403, 'Permission denied');
        });

        $middleware = new Permission;
        $request = Request::create('/admin/deny-path', 'GET');
        $route = new \Illuminate\Routing\Route('GET', 'admin/deny-path', function () {
            return 'test';
        });
        $request->setRouteResolver(fn () => $route);
        $this->app->instance('request', $request);

        $nextCalled = false;

        try {
            $middleware->handle($request, function () use (&$nextCalled) {
                $nextCalled = true;

                return 'next';
            });
            $this->fail('Expected HttpException 403');
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
            $this->assertSame(403, $e->getStatusCode());
        }

        $this->assertFalse($nextCalled);
    }

    public function test_handle_allows_when_user_in_menu_roles(): void
    {
        $this->app['config']->set('admin.permission.enable', true);
        $this->app['config']->set('admin.permission.except', []);
        $this->app['config']->set('admin.menu.role_bind_menu', true);
        FakeMenuPermissionQueryForMiddlewareTest::$exactResult = (object) [
            'id' => 10,
            'roles' => collect([(object) ['slug' => 'editor']]),
        ];

        $user = Mockery::mock(\Dcat\Admin\Models\Administrator::class)->makePartial();
        $user->shouldReceive('isAdministrator')->andReturn(false);
        $user->shouldReceive('allPermissions')->andReturn(collect());
        $user->shouldReceive('inRoles')->with(['editor'])->andReturn(true);

        $guard = Mockery::mock(\Illuminate\Contracts\Auth\StatefulGuard::class);
        $guard->shouldReceive('user')->andReturn($user);
        $guard->shouldReceive('check')->andReturn(true);
        $guard->shouldReceive('id')->andReturn(1);

        \Illuminate\Support\Facades\Auth::shouldReceive('guard')
            ->with('admin')
            ->andReturn($guard);

        $middleware = new Permission;
        $request = Request::create('/admin/allow-path', 'GET');
        $route = new \Illuminate\Routing\Route('GET', 'admin/allow-path', function () {
            return 'test';
        });
        $request->setRouteResolver(fn () => $route);
        $this->app->instance('request', $request);

        $called = false;
        $response = $middleware->handle($request, function () use (&$called) {
            $called = true;

            return 'next';
        });

        $this->assertTrue($called);
        $this->assertSame('next', $response);
    }
}
