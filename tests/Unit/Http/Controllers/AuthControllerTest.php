<?php

namespace Dcat\Admin\Tests\Unit\Http\Controllers;

use Dcat\Admin\Admin;
use Dcat\Admin\Http\Controllers\AuthController;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Tests\TestCase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
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

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_get_failed_login_message_returns_translation_when_available(): void
    {
        Lang::shouldReceive('has')->with('admin.auth_failed')->once()->andReturn(true);
        Lang::shouldReceive('get')->with('admin.auth_failed', [], null)->once()->andReturn('认证失败');

        $controller = new AuthController;
        $method = new \ReflectionMethod($controller, 'getFailedLoginMessage');
        $method->setAccessible(true);

        $result = $method->invoke($controller);

        $this->assertSame('认证失败', $result);
    }

    public function test_get_failed_login_message_returns_default_when_no_translation(): void
    {
        Lang::shouldReceive('has')->with('admin.auth_failed')->once()->andReturn(false);

        $controller = new AuthController;
        $method = new \ReflectionMethod($controller, 'getFailedLoginMessage');
        $method->setAccessible(true);

        $result = $method->invoke($controller);

        $this->assertSame('These credentials do not match our records.', $result);
    }

    public function test_get_redirect_path_returns_admin_root_by_default(): void
    {
        $controller = new AuthController;
        $method = new \ReflectionMethod($controller, 'getRedirectPath');
        $method->setAccessible(true);

        $result = $method->invoke($controller);

        $this->assertSame(admin_url('/'), $result);
    }

    public function test_get_login_returns_content_when_not_authenticated(): void
    {
        $guard = Mockery::mock(\Illuminate\Contracts\Auth\StatefulGuard::class);
        $guard->shouldReceive('check')->once()->andReturn(false);

        Auth::shouldReceive('guard')->with('admin')->andReturn($guard);

        $content = Mockery::mock(Content::class);
        $content->shouldReceive('full')->once()->andReturnSelf();
        $content->shouldReceive('body')->once()->andReturnSelf();

        $controller = new AuthController;
        $result = $controller->getLogin($content);

        $this->assertSame($content, $result);
    }

    public function test_get_login_redirects_when_already_authenticated(): void
    {
        $guard = Mockery::mock(\Illuminate\Contracts\Auth\StatefulGuard::class);
        $guard->shouldReceive('check')->once()->andReturn(true);

        Auth::shouldReceive('guard')->with('admin')->andReturn($guard);

        $content = Mockery::mock(Content::class);

        $controller = new AuthController;
        $result = $controller->getLogin($content);

        $this->assertInstanceOf(\Illuminate\Http\RedirectResponse::class, $result);
        $this->assertSame(admin_url('/'), $result->getTargetUrl());
    }

    public function test_get_logout_calls_guard_logout_and_invalidates_session(): void
    {
        $guard = Mockery::mock(\Illuminate\Contracts\Auth\StatefulGuard::class);
        $guard->shouldReceive('logout')->once();

        Auth::shouldReceive('guard')->with('admin')->andReturn($guard);

        $session = Mockery::mock(\Illuminate\Contracts\Session\Session::class);
        $session->shouldReceive('invalidate')->once();

        $request = Request::create('/auth/logout', 'GET');
        $request->setLaravelSession($session);
        $request->headers->remove('X-PJAX');

        $controller = new AuthController;
        $result = $controller->getLogout($request);

        $this->assertInstanceOf(\Illuminate\Http\RedirectResponse::class, $result);
        $this->assertSame(admin_url('auth/login'), $result->getTargetUrl());
    }

    public function test_post_login_returns_error_when_validation_fails(): void
    {
        $request = Request::create('/auth/login', 'POST', [
            'username' => '',
            'password' => '',
        ]);
        $request->headers->set('X-Requested-With', 'XMLHttpRequest');
        $this->app->instance('request', $request);

        $controller = new AuthController;
        $result = $controller->postLogin($request);

        $this->assertInstanceOf(\Illuminate\Http\JsonResponse::class, $result);

        $data = $result->getData(true);
        $this->assertNotEmpty($data);
    }

    public function test_post_login_returns_error_when_authentication_fails(): void
    {
        $guard = Mockery::mock(\Illuminate\Contracts\Auth\StatefulGuard::class);
        $guard->shouldReceive('attempt')
            ->with(['username' => 'admin', 'password' => 'wrong'], false)
            ->once()
            ->andReturn(false);

        Auth::shouldReceive('guard')->with('admin')->andReturn($guard);

        $request = Request::create('/auth/login', 'POST', [
            'username' => 'admin',
            'password' => 'wrong',
        ]);
        $request->headers->set('X-Requested-With', 'XMLHttpRequest');
        $this->app->instance('request', $request);

        $controller = new AuthController;
        $result = $controller->postLogin($request);

        $this->assertInstanceOf(\Illuminate\Http\JsonResponse::class, $result);

        $data = $result->getData(true);
        $this->assertNotEmpty($data);
    }

    public function test_guard_returns_admin_guard(): void
    {
        $controller = new AuthController;
        $method = new \ReflectionMethod($controller, 'guard');
        $method->setAccessible(true);

        $result = $method->invoke($controller);
        $expected = Admin::guard();

        $this->assertSame($expected, $result);
    }

    public function test_get_setting_method_exists_and_accepts_content(): void
    {
        $controller = new AuthController;
        $method = new \ReflectionMethod($controller, 'getSetting');

        $this->assertTrue($method->isPublic());
        $this->assertCount(1, $method->getParameters());
        $this->assertSame('content', $method->getParameters()[0]->getName());
    }

    public function test_put_setting_method_exists(): void
    {
        $controller = new AuthController;

        $this->assertTrue(method_exists($controller, 'putSetting'));

        $method = new \ReflectionMethod($controller, 'putSetting');
        $this->assertTrue($method->isPublic());
    }

    public function test_setting_form_returns_form_instance(): void
    {
        $this->app['config']->set('admin.database.users_model', \Dcat\Admin\Models\Administrator::class);

        $user = Mockery::mock(\Dcat\Admin\Models\Administrator::class)->makePartial();
        $user->shouldReceive('getKey')->andReturn(1);

        $guard = Mockery::mock(\Illuminate\Contracts\Auth\StatefulGuard::class);
        $guard->shouldReceive('user')->andReturn($user);

        Auth::shouldReceive('guard')->with('admin')->andReturn($guard);

        $controller = new AuthController;
        $method = new \ReflectionMethod($controller, 'settingForm');
        $method->setAccessible(true);

        $result = $method->invoke($controller);

        $this->assertInstanceOf(\Dcat\Admin\Form::class, $result);
    }

    public function test_send_login_response_regenerates_session(): void
    {
        $session = Mockery::mock(\Illuminate\Contracts\Session\Session::class);
        $session->shouldReceive('regenerate')->once();
        $session->shouldReceive('previousUrl')->andReturn(null);

        $request = Request::create('/auth/login', 'POST');
        $request->setLaravelSession($session);
        $this->app->instance('request', $request);

        $guard = Mockery::mock(\Illuminate\Contracts\Auth\StatefulGuard::class);
        $guard->shouldReceive('user')->andReturn(null);

        Auth::shouldReceive('guard')->with('admin')->andReturn($guard);

        $controller = new AuthController;
        $method = new \ReflectionMethod($controller, 'sendLoginResponse');
        $method->setAccessible(true);

        $result = $method->invoke($controller, $request);

        // sendLoginResponse returns a response; session regenerate should have been called
        $this->assertNotNull($result);
    }
}
