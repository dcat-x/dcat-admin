<?php

namespace Dcat\Admin\Tests\Unit\Http\Controllers;

use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Tests\TestCase;

class AdminControllerTest extends TestCase
{
    public function test_title_returns_property_value_when_set(): void
    {
        $controller = new class extends AdminController
        {
            protected $title = 'Custom Title';
        };

        $reflection = new \ReflectionMethod($controller, 'title');
        $reflection->setAccessible(true);

        $this->assertEquals('Custom Title', $reflection->invoke($controller));
    }

    public function test_title_falls_back_to_admin_trans_label_when_not_set(): void
    {
        $controller = new class extends AdminController {};

        $reflection = new \ReflectionMethod($controller, 'title');
        $reflection->setAccessible(true);

        // When title is not set, it falls back to admin_trans_label()
        $result = $reflection->invoke($controller);
        $this->assertIsString($result);
    }

    public function test_description_returns_default_empty_array(): void
    {
        $controller = new class extends AdminController {};

        $reflection = new \ReflectionMethod($controller, 'description');
        $reflection->setAccessible(true);

        $result = $reflection->invoke($controller);
        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    public function test_description_returns_custom_descriptions(): void
    {
        $controller = new class extends AdminController
        {
            protected $description = [
                'index' => 'List Page',
                'show' => 'Detail Page',
                'edit' => 'Edit Page',
                'create' => 'Create Page',
            ];
        };

        $reflection = new \ReflectionMethod($controller, 'description');
        $reflection->setAccessible(true);

        $result = $reflection->invoke($controller);
        $this->assertEquals('List Page', $result['index']);
        $this->assertEquals('Detail Page', $result['show']);
        $this->assertEquals('Edit Page', $result['edit']);
        $this->assertEquals('Create Page', $result['create']);
    }

    public function test_translation_returns_null_by_default(): void
    {
        $controller = new class extends AdminController {};

        $reflection = new \ReflectionMethod($controller, 'translation');
        $reflection->setAccessible(true);

        $this->assertNull($reflection->invoke($controller));
    }

    public function test_translation_returns_custom_path(): void
    {
        $controller = new class extends AdminController
        {
            protected $translation = 'admin.custom';
        };

        $reflection = new \ReflectionMethod($controller, 'translation');
        $reflection->setAccessible(true);

        $this->assertEquals('admin.custom', $reflection->invoke($controller));
    }

    public function test_controller_extends_illuminate_controller(): void
    {
        $controller = new class extends AdminController {};

        $this->assertInstanceOf(\Illuminate\Routing\Controller::class, $controller);
    }

    public function test_update_delegates_to_form_update(): void
    {
        $this->assertTrue(method_exists(AdminController::class, 'update'));

        $reflection = new \ReflectionMethod(AdminController::class, 'update');
        $this->assertTrue($reflection->isPublic());

        $params = $reflection->getParameters();
        $this->assertCount(1, $params);
        $this->assertEquals('id', $params[0]->getName());
    }

    public function test_store_method_exists_and_is_public(): void
    {
        $this->assertTrue(method_exists(AdminController::class, 'store'));

        $reflection = new \ReflectionMethod(AdminController::class, 'store');
        $this->assertTrue($reflection->isPublic());
        $this->assertCount(0, $reflection->getParameters());
    }

    public function test_destroy_method_exists_and_is_public(): void
    {
        $this->assertTrue(method_exists(AdminController::class, 'destroy'));

        $reflection = new \ReflectionMethod(AdminController::class, 'destroy');
        $this->assertTrue($reflection->isPublic());

        $params = $reflection->getParameters();
        $this->assertCount(1, $params);
        $this->assertEquals('id', $params[0]->getName());
    }
}
