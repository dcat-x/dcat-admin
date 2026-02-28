<?php

namespace Dcat\Admin\Tests\Unit\Http\Middleware;

use Dcat\Admin\Admin;
use Dcat\Admin\Http\Middleware\Permission;
use Dcat\Admin\Tests\TestCase;
use Illuminate\Http\Request;
use Mockery;

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
}
