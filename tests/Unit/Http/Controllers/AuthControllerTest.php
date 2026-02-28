<?php

namespace Dcat\Admin\Tests\Unit\Http\Controllers;

use Dcat\Admin\Http\Controllers\AuthController;
use Dcat\Admin\Tests\TestCase;
use Mockery;

class AuthControllerTest extends TestCase
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

    public function test_validate_returns_true_when_no_new_password(): void
    {
        $controller = new AuthController;
        $reflection = new \ReflectionMethod($controller, 'validateCredentialsWhenUpdatingPassword');
        $reflection->setAccessible(true);

        // 模拟请求中没有新密码
        $request = \Illuminate\Http\Request::create('/auth/setting', 'PUT', [
            'old_password' => '',
            'password' => '',
        ]);
        $this->app->instance('request', $request);

        // 使用 mock 用户
        $user = Mockery::mock(\Dcat\Admin\Models\Administrator::class)->makePartial();
        $user->shouldReceive('getKey')->andReturn(1);
        $user->shouldReceive('getAuthPassword')->andReturn(password_hash('old_pass', PASSWORD_BCRYPT));

        $guard = Mockery::mock(\Illuminate\Contracts\Auth\StatefulGuard::class);
        $guard->shouldReceive('user')->andReturn($user);

        \Illuminate\Support\Facades\Auth::shouldReceive('guard')->with('admin')->andReturn($guard);

        $result = $reflection->invoke($controller);

        // 没有新密码时应返回 true（跳过验证）
        $this->assertTrue($result);
    }

    public function test_validate_returns_true_when_new_password_matches_current(): void
    {
        $controller = new AuthController;
        $reflection = new \ReflectionMethod($controller, 'validateCredentialsWhenUpdatingPassword');
        $reflection->setAccessible(true);

        $currentPasswordPlain = 'current_password';
        $currentPasswordHash = password_hash($currentPasswordPlain, PASSWORD_BCRYPT);

        // 新密码与当前密码相同 → password_verify 返回 true → 方法返回 true
        $request = \Illuminate\Http\Request::create('/auth/setting', 'PUT', [
            'old_password' => '',
            'password' => $currentPasswordPlain,
        ]);
        $this->app->instance('request', $request);

        $user = Mockery::mock(\Dcat\Admin\Models\Administrator::class)->makePartial();
        $user->shouldReceive('getKey')->andReturn(1);
        $user->shouldReceive('getAuthPassword')->andReturn($currentPasswordHash);

        $guard = Mockery::mock(\Illuminate\Contracts\Auth\StatefulGuard::class);
        $guard->shouldReceive('user')->andReturn($user);

        \Illuminate\Support\Facades\Auth::shouldReceive('guard')->with('admin')->andReturn($guard);

        $result = $reflection->invoke($controller);

        // 新密码与当前密码相同时，password_verify 返回 true，方法应返回 true
        $this->assertTrue($result);
    }

    public function test_validate_returns_false_when_different_password_and_no_old_password(): void
    {
        $controller = new AuthController;
        $reflection = new \ReflectionMethod($controller, 'validateCredentialsWhenUpdatingPassword');
        $reflection->setAccessible(true);

        $currentPasswordHash = password_hash('current_password', PASSWORD_BCRYPT);

        // 新密码不同于当前密码，且未提供旧密码
        $request = \Illuminate\Http\Request::create('/auth/setting', 'PUT', [
            'old_password' => '',
            'password' => 'new_different_password',
        ]);
        $this->app->instance('request', $request);

        $user = Mockery::mock(\Dcat\Admin\Models\Administrator::class)->makePartial();
        $user->shouldReceive('getKey')->andReturn(1);
        $user->shouldReceive('getAuthPassword')->andReturn($currentPasswordHash);

        $guard = Mockery::mock(\Illuminate\Contracts\Auth\StatefulGuard::class);
        $guard->shouldReceive('user')->andReturn($user);

        \Illuminate\Support\Facades\Auth::shouldReceive('guard')->with('admin')->andReturn($guard);

        $result = $reflection->invoke($controller);

        // 新密码不同 + 无旧密码 → 返回 false
        $this->assertFalse($result);
    }

    public function test_validate_delegates_to_provider_when_old_password_provided(): void
    {
        $controller = new AuthController;
        $reflection = new \ReflectionMethod($controller, 'validateCredentialsWhenUpdatingPassword');
        $reflection->setAccessible(true);

        $currentPasswordHash = password_hash('current_password', PASSWORD_BCRYPT);

        // 新密码不同，提供了旧密码
        $request = \Illuminate\Http\Request::create('/auth/setting', 'PUT', [
            'old_password' => 'current_password',
            'password' => 'new_different_password',
        ]);
        $this->app->instance('request', $request);

        $user = Mockery::mock(\Dcat\Admin\Models\Administrator::class)->makePartial();
        $user->shouldReceive('getKey')->andReturn(1);
        $user->shouldReceive('getAuthPassword')->andReturn($currentPasswordHash);

        $provider = Mockery::mock(\Illuminate\Contracts\Auth\UserProvider::class);
        $provider->shouldReceive('validateCredentials')
            ->with($user, ['password' => 'current_password'])
            ->once()
            ->andReturn(true);

        $guard = Mockery::mock(\Illuminate\Contracts\Auth\StatefulGuard::class);
        $guard->shouldReceive('user')->andReturn($user);
        $guard->shouldReceive('getProvider')->andReturn($provider);

        \Illuminate\Support\Facades\Auth::shouldReceive('guard')->with('admin')->andReturn($guard);

        $result = $reflection->invoke($controller);

        // 应委托给 provider 验证旧密码
        $this->assertTrue($result);
    }

    public function test_controller_has_required_methods(): void
    {
        $controller = new AuthController;

        $this->assertTrue(method_exists($controller, 'getLogin'));
        $this->assertTrue(method_exists($controller, 'postLogin'));
        $this->assertTrue(method_exists($controller, 'getLogout'));
        $this->assertTrue(method_exists($controller, 'getSetting'));
        $this->assertTrue(method_exists($controller, 'putSetting'));
    }

    public function test_username_returns_username(): void
    {
        $controller = new AuthController;
        $reflection = new \ReflectionMethod($controller, 'username');
        $reflection->setAccessible(true);

        $this->assertEquals('username', $reflection->invoke($controller));
    }
}
