<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Http\Controllers;

use Dcat\Admin\Exception\AdminException;
use Dcat\Admin\Http\Controllers\HandleFormController;
use Dcat\Admin\Tests\TestCase;
use Dcat\Admin\Widgets\Form;
use Illuminate\Http\Request;

// 用于测试的非 Form 类
class NotAForm
{
    public function handle()
    {
        return 'not a form';
    }
}

class HandleFormControllerTest extends TestCase
{
    protected HandleFormController $controller;

    protected function setUp(): void
    {
        parent::setUp();

        $this->controller = new class extends HandleFormController
        {
            public function exposeGetField(Request $request, $form)
            {
                return $this->getField($request, $form);
            }
        };
    }

    public function test_throws_exception_when_form_param_missing(): void
    {
        $request = Request::create('/handle-form', 'POST', []);

        $this->expectException(AdminException::class);
        $this->expectExceptionMessage('Invalid form request.');

        $this->controller->handle($request);
    }

    public function test_throws_exception_when_form_class_not_exists(): void
    {
        $request = Request::create('/handle-form', 'POST', [
            Form::REQUEST_NAME => 'Non\\Existent\\FormClass',
        ]);

        $this->expectException(AdminException::class);
        $this->expectExceptionMessage('does not exist');

        $this->controller->handle($request);
    }

    public function test_throws_exception_when_class_is_not_form_instance(): void
    {
        $request = Request::create('/handle-form', 'POST', [
            Form::REQUEST_NAME => NotAForm::class,
        ]);

        $this->expectException(AdminException::class);
        $this->expectExceptionMessage('must be an instance of');

        $this->controller->handle($request);
    }

    public function test_get_field_returns_null_for_nonexistent_column(): void
    {
        $request = Request::create('/handle-form/upload', 'POST', [
            Form::REQUEST_NAME => StubWidgetForm::class,
            '_column' => 'nonexistent_column',
        ]);
        $this->app->instance('request', $request);

        // StubWidgetForm 没有字段定义，任何字段名查找都应返回 null
        $form = new StubWidgetForm;

        $field = $this->controller->exposeGetField($request, $form);
        $this->assertNull($field, 'getField should return null for nonexistent column');
    }

    public function test_get_field_returns_null_for_nonexistent_relation(): void
    {
        $request = Request::create('/handle-form/upload', 'POST', [
            Form::REQUEST_NAME => StubWidgetForm::class,
            '_column' => 'some_column',
            '_relation' => 'nonexistent_relation',
        ]);
        $this->app->instance('request', $request);

        $form = new StubWidgetForm;

        // 关联字段不存在时也应返回 null
        $field = $this->controller->exposeGetField($request, $form);
        $this->assertNull($field, 'getField should return null for nonexistent relation');
    }

    public function test_upload_file_throws_exception_when_form_param_missing(): void
    {
        $request = Request::create('/handle-form/upload', 'POST', []);

        $this->expectException(AdminException::class);
        $this->expectExceptionMessage('Invalid form request.');

        $this->controller->uploadFile($request);
    }

    public function test_destroy_file_throws_exception_when_form_param_missing(): void
    {
        $request = Request::create('/handle-form/destroy', 'POST', []);

        $this->expectException(AdminException::class);
        $this->expectExceptionMessage('Invalid form request.');

        $this->controller->destroyFile($request);
    }
}

// 用于测试的合法 Form 子类
class StubWidgetForm extends Form
{
    public function handle(array $input)
    {
        return $this->response()->success('ok');
    }
}
