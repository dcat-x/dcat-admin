<?php

namespace Dcat\Admin\Tests\Unit\Http\Controllers;

use Dcat\Admin\Http\Controllers\ValueController;
use Dcat\Admin\Tests\TestCase;
use Illuminate\Http\Request;

// 没有 passesAuthorization 方法的类
class IncompleteHandler
{
    public function handle()
    {
        return 'result';
    }
}

// 没有 handle 方法的类
class NoHandleClass
{
    public function passesAuthorization()
    {
        return true;
    }
}

// 合法的值请求处理器
class ValidValueHandler
{
    public function handle(Request $request)
    {
        return 'value result';
    }

    public function passesAuthorization()
    {
        return true;
    }

    public function failedAuthorization()
    {
        return 'unauthorized';
    }
}

class ValueControllerTest extends TestCase
{
    protected ValueController $controller;

    protected function setUp(): void
    {
        parent::setUp();

        $this->controller = new ValueController;
    }

    public function test_throws_exception_when_key_missing(): void
    {
        $request = Request::create('/value', 'GET', []);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Invalid request.');

        $this->controller->handle($request);
    }

    public function test_throws_exception_when_class_not_exists(): void
    {
        $request = Request::create('/value', 'GET', [
            '_key' => 'Non\\Existent\\ClassName',
        ]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('does not exist');

        $this->controller->handle($request);
    }

    public function test_throws_exception_when_class_missing_handle_method(): void
    {
        $request = Request::create('/value', 'GET', [
            '_key' => NoHandleClass::class,
        ]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('is not a valid value request handler');

        $this->controller->handle($request);
    }

    public function test_throws_exception_when_class_missing_passes_authorization(): void
    {
        $request = Request::create('/value', 'GET', [
            '_key' => IncompleteHandler::class,
        ]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('is not a valid value request handler');

        $this->controller->handle($request);
    }

    public function test_resolves_valid_handler(): void
    {
        $request = Request::create('/value', 'GET', [
            '_key' => ValidValueHandler::class,
        ]);

        $result = $this->controller->handle($request);

        $this->assertEquals('value result', $result);
    }
}
