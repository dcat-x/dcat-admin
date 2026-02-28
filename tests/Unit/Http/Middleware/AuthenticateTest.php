<?php

namespace Dcat\Admin\Tests\Unit\Http\Middleware;

use Dcat\Admin\Http\Middleware\Authenticate;
use Dcat\Admin\Tests\TestCase;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Mockery;

class AuthenticateTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->app['config']->set('admin.auth.enable', true);
        $this->app['config']->set('admin.auth.guard', 'admin');
        $this->app['config']->set('admin.route.prefix', 'admin');
        $this->app['config']->set('auth.guards.admin', [
            'driver' => 'session',
            'provider' => 'admin',
        ]);
        $this->app['config']->set('auth.providers.admin', [
            'driver' => 'eloquent',
            'model' => \Dcat\Admin\Models\Administrator::class,
        ]);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_handle_passes_through_when_auth_disabled(): void
    {
        $this->app['config']->set('admin.auth.enable', false);

        $middleware = new Authenticate;
        $request = Request::create('/admin/test', 'GET');
        $response = new Response('OK', 200);

        $result = $middleware->handle($request, function () use ($response) {
            return $response;
        });

        $this->assertSame($response, $result);
    }

    public function test_handle_passes_through_when_user_authenticated(): void
    {
        $guard = Mockery::mock(\Illuminate\Contracts\Auth\StatefulGuard::class);
        $guard->shouldReceive('guest')->andReturn(false);

        \Illuminate\Support\Facades\Auth::shouldReceive('guard')
            ->with('admin')
            ->andReturn($guard);

        $middleware = new Authenticate;
        $request = Request::create('/admin/dashboard', 'GET');
        $response = new Response('OK', 200);

        $result = $middleware->handle($request, function () use ($response) {
            return $response;
        });

        $this->assertSame($response, $result);
    }

    public function test_handle_redirects_guest_to_login(): void
    {
        $guard = Mockery::mock(\Illuminate\Contracts\Auth\StatefulGuard::class);
        $guard->shouldReceive('guest')->andReturn(true);

        \Illuminate\Support\Facades\Auth::shouldReceive('guard')
            ->with('admin')
            ->andReturn($guard);

        $this->app['config']->set('admin.auth.except', []);

        $middleware = new Authenticate;
        $request = Request::create('/admin/dashboard', 'GET');

        $result = $middleware->handle($request, function () {
            return new Response('OK', 200);
        });

        // Should not return the original response - should redirect
        $this->assertNotEquals(200, $result->getStatusCode());
    }

    public function test_should_pass_through_returns_false_for_empty_excepts(): void
    {
        $this->app['config']->set('admin.auth.except', []);

        $request = Request::create('/admin/dashboard', 'GET');

        $result = Authenticate::shouldPassThrough($request);

        $this->assertFalse($result);
    }

    public function test_should_pass_through_is_static_method(): void
    {
        $reflection = new \ReflectionMethod(Authenticate::class, 'shouldPassThrough');

        $this->assertTrue($reflection->isStatic());
        $this->assertTrue($reflection->isPublic());
    }

    public function test_should_pass_through_accepts_request_parameter(): void
    {
        $reflection = new \ReflectionMethod(Authenticate::class, 'shouldPassThrough');
        $params = $reflection->getParameters();

        $this->assertCount(1, $params);
        $this->assertEquals('request', $params[0]->getName());
    }

    public function test_handle_method_signature(): void
    {
        $reflection = new \ReflectionMethod(Authenticate::class, 'handle');
        $params = $reflection->getParameters();

        $this->assertCount(2, $params);
        $this->assertEquals('request', $params[0]->getName());
        $this->assertEquals('next', $params[1]->getName());
    }

    public function test_middleware_class_is_not_abstract(): void
    {
        $reflection = new \ReflectionClass(Authenticate::class);

        $this->assertFalse($reflection->isAbstract());
    }
}
