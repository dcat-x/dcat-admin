<?php

namespace Dcat\Admin\Tests\Unit\Http\Middleware;

use Dcat\Admin\Http\Middleware\Session;
use Dcat\Admin\Tests\TestCase;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SessionTest extends TestCase
{
    public function test_handle_passes_through_when_session_middleware_disabled(): void
    {
        $this->app['config']->set('admin.route.enable_session_middleware', false);
        $this->app['config']->set('admin.multi_app', false);

        $middleware = new Session;
        $request = Request::create('/admin/test', 'GET');

        $called = false;
        $response = $middleware->handle($request, function ($req) use (&$called) {
            $called = true;

            return new Response('OK');
        });

        $this->assertTrue($called);
        // Session path should NOT have been modified
        $this->assertNotEquals('/admin', config('session.path'));
    }

    public function test_handle_sets_session_path_when_enabled(): void
    {
        $this->app['config']->set('admin.route.enable_session_middleware', true);
        $this->app['config']->set('admin.route.prefix', 'admin');
        $this->app['config']->set('app.url', 'http://localhost');

        $middleware = new Session;
        $request = Request::create('/admin/test', 'GET');

        $middleware->handle($request, function () {
            return new Response('OK');
        });

        $this->assertSame('/admin', config('session.path'));
    }

    public function test_handle_sets_session_path_when_multi_app(): void
    {
        $this->app['config']->set('admin.route.enable_session_middleware', false);
        $this->app['config']->set('admin.multi_app', true);
        $this->app['config']->set('admin.route.prefix', 'admin');
        $this->app['config']->set('app.url', 'http://localhost');

        $middleware = new Session;
        $request = Request::create('/admin/test', 'GET');

        $middleware->handle($request, function () {
            return new Response('OK');
        });

        $this->assertSame('/admin', config('session.path'));
    }

    public function test_handle_includes_app_url_path_prefix(): void
    {
        $this->app['config']->set('admin.route.enable_session_middleware', true);
        $this->app['config']->set('admin.route.prefix', 'admin');
        $this->app['config']->set('app.url', 'http://localhost/sub-path');

        $middleware = new Session;
        $request = Request::create('/admin/test', 'GET');

        $middleware->handle($request, function () {
            return new Response('OK');
        });

        $this->assertSame('/sub-path/admin', config('session.path'));
    }

    public function test_handle_trims_trailing_slash_from_app_path(): void
    {
        $this->app['config']->set('admin.route.enable_session_middleware', true);
        $this->app['config']->set('admin.route.prefix', 'admin');
        $this->app['config']->set('app.url', 'http://localhost/base/');

        $middleware = new Session;
        $request = Request::create('/admin/test', 'GET');

        $middleware->handle($request, function () {
            return new Response('OK');
        });

        $this->assertSame('/base/admin', config('session.path'));
    }

    public function test_handle_trims_admin_prefix_slashes(): void
    {
        $this->app['config']->set('admin.route.enable_session_middleware', true);
        $this->app['config']->set('admin.route.prefix', '/dashboard/');
        $this->app['config']->set('app.url', 'http://localhost');

        $middleware = new Session;
        $request = Request::create('/admin/test', 'GET');

        $middleware->handle($request, function () {
            return new Response('OK');
        });

        $this->assertSame('/dashboard', config('session.path'));
    }

    public function test_handle_returns_next_response(): void
    {
        $this->app['config']->set('admin.route.enable_session_middleware', true);
        $this->app['config']->set('admin.route.prefix', 'admin');
        $this->app['config']->set('app.url', 'http://localhost');

        $middleware = new Session;
        $request = Request::create('/admin/test', 'GET');

        $expected = new Response('Expected Response');
        $result = $middleware->handle($request, function () use ($expected) {
            return $expected;
        });

        $this->assertSame($expected, $result);
    }

    public function test_handle_with_no_path_in_app_url(): void
    {
        $this->app['config']->set('admin.route.enable_session_middleware', true);
        $this->app['config']->set('admin.route.prefix', 'backend');
        $this->app['config']->set('app.url', 'http://example.com');

        $middleware = new Session;
        $request = Request::create('/backend/test', 'GET');

        $middleware->handle($request, function () {
            return new Response('OK');
        });

        $this->assertSame('/backend', config('session.path'));
    }
}
