<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Http\Controllers;

use Dcat\Admin\Admin;
use Dcat\Admin\Form;
use Dcat\Admin\Http\Controllers\AuthController;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Models\Administrator;
use Dcat\Admin\Tests\TestCase;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Contracts\Session\Session;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Mockery;

class AuthControllerTest extends TestCase
{
    protected function makeController(): AuthController
    {
        return new class extends AuthController
        {
            public function exposeValidateCredentialsWhenUpdatingPassword()
            {
                return $this->validateCredentialsWhenUpdatingPassword();
            }

            public function exposeUsername(): string
            {
                return $this->username();
            }

            public function exposeFailedLoginMessage(): string
            {
                return $this->getFailedLoginMessage();
            }

            public function exposeRedirectPath(): string
            {
                return $this->getRedirectPath();
            }

            public function exposeGuard()
            {
                return $this->guard();
            }

            public function exposeSettingForm()
            {
                return $this->settingForm();
            }

            public function exposeSendLoginResponse(Request $request)
            {
                return $this->sendLoginResponse($request);
            }
        };
    }

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

    public function test_validate_returns_true_when_no_new_password(): void
    {
        $controller = $this->makeController();

        // 模拟请求中没有新密码
        $request = Request::create('/auth/setting', 'PUT', [
            'old_password' => '',
            'password' => '',
        ]);
        $this->app->instance('request', $request);

        // 使用 mock 用户
        $user = Mockery::mock(Administrator::class)->makePartial();
        $user->shouldReceive('getKey')->andReturn(1);
        $user->shouldReceive('getAuthPassword')->andReturn(password_hash('old_pass', PASSWORD_BCRYPT));

        $guard = Mockery::mock(StatefulGuard::class);
        $guard->shouldReceive('user')->andReturn($user);

        Auth::shouldReceive('guard')->with('admin')->andReturn($guard);

        $result = $controller->exposeValidateCredentialsWhenUpdatingPassword();

        // 没有新密码时应返回 true（跳过验证）
        $this->assertTrue($result);
    }

    public function test_validate_returns_true_when_new_password_matches_current(): void
    {
        $controller = $this->makeController();

        $currentPasswordPlain = 'current_password';
        $currentPasswordHash = password_hash($currentPasswordPlain, PASSWORD_BCRYPT);

        // 新密码与当前密码相同 → password_verify 返回 true → 方法返回 true
        $request = Request::create('/auth/setting', 'PUT', [
            'old_password' => '',
            'password' => $currentPasswordPlain,
        ]);
        $this->app->instance('request', $request);

        $user = Mockery::mock(Administrator::class)->makePartial();
        $user->shouldReceive('getKey')->andReturn(1);
        $user->shouldReceive('getAuthPassword')->andReturn($currentPasswordHash);

        $guard = Mockery::mock(StatefulGuard::class);
        $guard->shouldReceive('user')->andReturn($user);

        Auth::shouldReceive('guard')->with('admin')->andReturn($guard);

        $result = $controller->exposeValidateCredentialsWhenUpdatingPassword();

        // 新密码与当前密码相同时，password_verify 返回 true，方法应返回 true
        $this->assertTrue($result);
    }

    public function test_validate_returns_false_when_different_password_and_no_old_password(): void
    {
        $controller = $this->makeController();

        $currentPasswordHash = password_hash('current_password', PASSWORD_BCRYPT);

        // 新密码不同于当前密码，且未提供旧密码
        $request = Request::create('/auth/setting', 'PUT', [
            'old_password' => '',
            'password' => 'new_different_password',
        ]);
        $this->app->instance('request', $request);

        $user = Mockery::mock(Administrator::class)->makePartial();
        $user->shouldReceive('getKey')->andReturn(1);
        $user->shouldReceive('getAuthPassword')->andReturn($currentPasswordHash);

        $guard = Mockery::mock(StatefulGuard::class);
        $guard->shouldReceive('user')->andReturn($user);

        Auth::shouldReceive('guard')->with('admin')->andReturn($guard);

        $result = $controller->exposeValidateCredentialsWhenUpdatingPassword();

        // 新密码不同 + 无旧密码 → 返回 false
        $this->assertFalse($result);
    }

    public function test_validate_delegates_to_provider_when_old_password_provided(): void
    {
        $controller = $this->makeController();

        $currentPasswordHash = password_hash('current_password', PASSWORD_BCRYPT);

        // 新密码不同，提供了旧密码
        $request = Request::create('/auth/setting', 'PUT', [
            'old_password' => 'current_password',
            'password' => 'new_different_password',
        ]);
        $this->app->instance('request', $request);

        $user = Mockery::mock(Administrator::class)->makePartial();
        $user->shouldReceive('getKey')->andReturn(1);
        $user->shouldReceive('getAuthPassword')->andReturn($currentPasswordHash);

        $provider = Mockery::mock(UserProvider::class);
        $provider->shouldReceive('validateCredentials')
            ->with($user, ['password' => 'current_password'])
            ->once()
            ->andReturn(true);

        $guard = Mockery::mock(StatefulGuard::class);
        $guard->shouldReceive('user')->andReturn($user);
        $guard->shouldReceive('getProvider')->andReturn($provider);

        Auth::shouldReceive('guard')->with('admin')->andReturn($guard);

        $result = $controller->exposeValidateCredentialsWhenUpdatingPassword();

        // 应委托给 provider 验证旧密码
        $this->assertTrue($result);
    }

    public function test_get_setting_returns_content_with_form_edit_body(): void
    {
        $user = Mockery::mock(Administrator::class)->makePartial();
        $user->shouldReceive('getKey')->andReturn(1);

        $guard = Mockery::mock(StatefulGuard::class);
        $guard->shouldReceive('user')->andReturn($user);
        Auth::shouldReceive('guard')->with('admin')->andReturn($guard);

        $form = Mockery::mock(Form::class);
        $form->shouldReceive('tools')->andReturnSelf();
        $form->shouldReceive('edit')->with(1)->andReturn('form-body');

        $controller = new class($form) extends AuthController
        {
            public function __construct(private $mockForm) {}

            protected function settingForm()
            {
                return $this->mockForm;
            }
        };

        $content = Mockery::mock(Content::class);
        $content->shouldReceive('title')->once()->andReturnSelf();
        $content->shouldReceive('body')->once()->with('form-body')->andReturnSelf();

        $result = $controller->getSetting($content);

        $this->assertSame($content, $result);
    }

    public function test_put_setting_returns_form_update_response_when_validation_passes(): void
    {
        $user = Mockery::mock(Administrator::class)->makePartial();
        $user->shouldReceive('getKey')->andReturn(8);

        $guard = Mockery::mock(StatefulGuard::class);
        $guard->shouldReceive('user')->andReturn($user);
        Auth::shouldReceive('guard')->with('admin')->andReturn($guard);

        $form = Mockery::mock(Form::class);
        $form->shouldReceive('update')->once()->with(8)->andReturn('updated-response');
        $form->shouldReceive('responseValidationMessages')->never();

        $controller = new class($form) extends AuthController
        {
            public function __construct(private $mockForm) {}

            protected function settingForm()
            {
                return $this->mockForm;
            }

            protected function validateCredentialsWhenUpdatingPassword()
            {
                return true;
            }
        };

        $this->assertSame('updated-response', $controller->putSetting());
    }

    public function test_put_setting_adds_validation_message_when_old_password_invalid(): void
    {
        $user = Mockery::mock(Administrator::class)->makePartial();
        $user->shouldReceive('getKey')->andReturn(9);

        $guard = Mockery::mock(StatefulGuard::class);
        $guard->shouldReceive('user')->andReturn($user);
        Auth::shouldReceive('guard')->with('admin')->andReturn($guard);

        $form = Mockery::mock(Form::class);
        $form->shouldReceive('responseValidationMessages')->once()->with('old_password', trans('admin.old_password_error'));
        $form->shouldReceive('update')->once()->with(9)->andReturn('updated-with-validation-message');

        $controller = new class($form) extends AuthController
        {
            public function __construct(private $mockForm) {}

            protected function settingForm()
            {
                return $this->mockForm;
            }

            protected function validateCredentialsWhenUpdatingPassword()
            {
                return false;
            }
        };

        $this->assertSame('updated-with-validation-message', $controller->putSetting());
    }

    public function test_username_returns_username(): void
    {
        $controller = $this->makeController();

        $this->assertSame('username', $controller->exposeUsername());
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

        $controller = $this->makeController();
        $result = $controller->exposeFailedLoginMessage();

        $this->assertSame('认证失败', $result);
    }

    public function test_get_failed_login_message_returns_default_when_no_translation(): void
    {
        Lang::shouldReceive('has')->with('admin.auth_failed')->once()->andReturn(false);

        $controller = $this->makeController();
        $result = $controller->exposeFailedLoginMessage();

        $this->assertSame('These credentials do not match our records.', $result);
    }

    public function test_get_redirect_path_returns_admin_root_by_default(): void
    {
        $controller = $this->makeController();
        $result = $controller->exposeRedirectPath();

        $this->assertSame(admin_url('/'), $result);
    }

    public function test_get_login_returns_content_when_not_authenticated(): void
    {
        $guard = Mockery::mock(StatefulGuard::class);
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
        $guard = Mockery::mock(StatefulGuard::class);
        $guard->shouldReceive('check')->once()->andReturn(true);

        Auth::shouldReceive('guard')->with('admin')->andReturn($guard);

        $content = Mockery::mock(Content::class);

        $controller = new AuthController;
        $result = $controller->getLogin($content);

        $this->assertInstanceOf(RedirectResponse::class, $result);
        $this->assertSame(admin_url('/'), $result->getTargetUrl());
    }

    public function test_get_logout_calls_guard_logout_and_invalidates_session(): void
    {
        $guard = Mockery::mock(StatefulGuard::class);
        $guard->shouldReceive('logout')->once();

        Auth::shouldReceive('guard')->with('admin')->andReturn($guard);

        $session = Mockery::mock(Session::class);
        $session->shouldReceive('invalidate')->once();

        $request = Request::create('/auth/logout', 'GET');
        $request->setLaravelSession($session);
        $request->headers->remove('X-PJAX');

        $controller = new AuthController;
        $result = $controller->getLogout($request);

        $this->assertInstanceOf(RedirectResponse::class, $result);
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

        $this->assertInstanceOf(JsonResponse::class, $result);

        $data = $result->getData(true);
        $this->assertNotEmpty($data);
    }

    public function test_post_login_returns_error_when_authentication_fails(): void
    {
        $guard = Mockery::mock(StatefulGuard::class);
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

        $this->assertInstanceOf(JsonResponse::class, $result);

        $data = $result->getData(true);
        $this->assertNotEmpty($data);
    }

    public function test_guard_returns_admin_guard(): void
    {
        $controller = $this->makeController();
        $result = $controller->exposeGuard();
        $expected = Admin::guard();

        $this->assertSame($expected, $result);
    }

    public function test_setting_form_returns_form_instance(): void
    {
        $this->app['config']->set('admin.database.users_model', Administrator::class);

        $user = Mockery::mock(Administrator::class)->makePartial();
        $user->shouldReceive('getKey')->andReturn(1);

        $guard = Mockery::mock(StatefulGuard::class);
        $guard->shouldReceive('user')->andReturn($user);

        Auth::shouldReceive('guard')->with('admin')->andReturn($guard);

        $controller = $this->makeController();
        $result = $controller->exposeSettingForm();

        $this->assertInstanceOf(Form::class, $result);
    }

    public function test_send_login_response_regenerates_session(): void
    {
        $session = Mockery::mock(Session::class);
        $session->shouldReceive('regenerate')->once();
        $session->shouldReceive('previousUrl')->andReturn(null);

        $request = Request::create('/auth/login', 'POST');
        $request->setLaravelSession($session);
        $this->app->instance('request', $request);

        $guard = Mockery::mock(StatefulGuard::class);
        $guard->shouldReceive('user')->andReturn(null);

        Auth::shouldReceive('guard')->with('admin')->andReturn($guard);

        $controller = $this->makeController();
        $result = $controller->exposeSendLoginResponse($request);

        // sendLoginResponse returns a response; session regenerate should have been called
        $this->assertNotNull($result);
    }
}
