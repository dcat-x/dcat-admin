<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Http\Controllers;

use Dcat\Admin\Actions\Action;
use Dcat\Admin\Exception\AdminException;
use Dcat\Admin\Http\Controllers\HandleActionController;
use Dcat\Admin\Models\Administrator;
use Dcat\Admin\Tests\TestCase;
use Illuminate\Http\Request;

// 用于测试的合法 Action 子类
class StubAction extends Action
{
    public function handle(Request $request)
    {
        return 'handled';
    }
}

// 用于测试的非 Action 类
class NotAnAction
{
    public function handle()
    {
        return 'not an action';
    }
}

class HandleActionControllerTest extends TestCase
{
    protected HandleActionController $controller;

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

        $this->controller = new HandleActionController;
    }

    public function test_throws_exception_when_action_param_missing(): void
    {
        $request = Request::create('/handle-action', 'POST', []);

        $this->expectException(AdminException::class);
        $this->expectExceptionMessage('Invalid action request.');

        $this->controller->handle($request);
    }

    public function test_throws_exception_when_action_class_not_exists(): void
    {
        $request = Request::create('/handle-action', 'POST', [
            '_action' => 'Non_Existent_Class',
        ]);

        $this->expectException(AdminException::class);
        $this->expectExceptionMessage('does not exist');

        $this->controller->handle($request);
    }

    public function test_throws_exception_when_class_is_not_action_instance(): void
    {
        $className = str_replace('\\', '_', NotAnAction::class);

        $request = Request::create('/handle-action', 'POST', [
            '_action' => $className,
        ]);

        $this->expectException(AdminException::class);
        $this->expectExceptionMessage('must be an instance of');

        $this->controller->handle($request);
    }

    public function test_resolves_valid_action_class(): void
    {
        $className = str_replace('\\', '_', StubAction::class);

        $request = Request::create('/handle-action', 'POST', [
            '_action' => $className,
        ]);

        // StubAction 是有效的 Action 子类，应正常处理
        $result = $this->controller->handle($request);

        $this->assertNotNull($result);
    }

    public function test_action_class_name_underscore_to_namespace(): void
    {
        // 确保下划线到命名空间的转换正确
        $input = 'Dcat_Admin_Tests_Unit_Http_Controllers_StubAction';
        $expected = StubAction::class;

        $this->assertSame($expected, str_replace('_', '\\', $input));
    }
}
