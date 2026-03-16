<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Http\Middleware;

use Dcat\Admin\Http\Middleware\Pjax;
use Dcat\Admin\Models\Administrator;
use Dcat\Admin\Tests\TestCase;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Mockery;

class PjaxTest extends TestCase
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
            'model' => Administrator::class,
        ]);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_handle_passes_through_for_non_pjax_request(): void
    {
        $middleware = new Pjax;
        $request = Request::create('/admin/test', 'GET');

        $response = new Response('OK', 200);
        $result = $middleware->handle($request, function () use ($response) {
            return $response;
        });

        $this->assertSame($response, $result);
    }

    public function test_handle_passes_through_for_redirect_response(): void
    {
        $middleware = new Pjax;
        $request = Request::create('/admin/test', 'GET');
        $request->headers->set('X-PJAX', 'true');

        $response = new RedirectResponse('/admin/other');
        $result = $middleware->handle($request, function () use ($response) {
            return $response;
        });

        $this->assertSame($response, $result);
    }

    public function test_handle_passes_through_when_guest(): void
    {
        $middleware = new Pjax;
        $request = Request::create('/admin/test', 'GET');
        $request->headers->set('X-PJAX', 'true');

        $response = new Response('OK', 200);
        $result = $middleware->handle($request, function () use ($response) {
            return $response;
        });

        // Guest user, should pass through without modification
        $this->assertSame($response, $result);
    }

    public function test_handle_sets_pjax_url_header_for_authenticated_pjax(): void
    {
        // Mock authenticated user
        $guard = Mockery::mock(StatefulGuard::class);
        $guard->shouldReceive('guest')->andReturn(false);
        $guard->shouldReceive('user')->andReturn(Mockery::mock(Administrator::class));
        $guard->shouldReceive('check')->andReturn(true);
        $guard->shouldReceive('id')->andReturn(1);

        Auth::shouldReceive('guard')
            ->with('admin')
            ->andReturn($guard);

        $middleware = new Pjax;
        $request = Request::create('/admin/dashboard', 'GET');
        $request->headers->set('X-PJAX', 'true');

        $response = new Response('OK', 200);
        $result = $middleware->handle($request, function () use ($response) {
            return $response;
        });

        $this->assertTrue($result->headers->has('X-PJAX-URL'));
        $this->assertSame('/admin/dashboard', $result->headers->get('X-PJAX-URL'));
    }

    public function test_set_uri_header_method(): void
    {
        $middleware = new Pjax;

        $response = new Response('OK');
        $request = Request::create('/admin/users?page=2', 'GET');

        $reflection = new \ReflectionClass($middleware);
        $method = $reflection->getMethod('setUriHeader');
        $method->setAccessible(true);
        $method->invoke($middleware, $response, $request);

        $this->assertSame('/admin/users?page=2', $response->headers->get('X-PJAX-URL'));
    }

    public function test_handle_error_response_in_debug_mode(): void
    {
        $this->app['config']->set('app.debug', true);

        $middleware = new Pjax;

        $exception = new \RuntimeException('Test error');
        $response = new Response('Error', 500);
        $response->exception = $exception;

        $reflection = new \ReflectionClass($middleware);
        $method = $reflection->getMethod('handleErrorResponse');
        $method->setAccessible(true);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Test error');

        $method->invoke($middleware, $response);
    }

    public function test_handle_error_response_in_production_mode(): void
    {
        $this->app['config']->set('app.debug', false);

        $middleware = new Pjax;

        $exception = new \RuntimeException('Production error');
        $response = new Response('Error', 500);
        $response->exception = $exception;

        $reflection = new \ReflectionClass($middleware);
        $method = $reflection->getMethod('handleErrorResponse');
        $method->setAccessible(true);

        $result = $method->invoke($middleware, $response);

        // Should return a redirect response
        $this->assertInstanceOf(RedirectResponse::class, $result);
    }
}
