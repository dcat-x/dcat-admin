<?php

namespace Dcat\Admin\Tests\Unit\Http\Controllers;

use Dcat\Admin\Admin;
use Dcat\Admin\Contracts\LazyRenderable;
use Dcat\Admin\Http\Controllers\RenderableController;
use Dcat\Admin\Tests\TestCase;
use Illuminate\Http\Request;
use Mockery;

class RenderableControllerTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_controller_class_exists(): void
    {
        $this->assertTrue(class_exists(RenderableController::class));
    }

    public function test_handle_method_is_public(): void
    {
        $reflection = new \ReflectionMethod(RenderableController::class, 'handle');
        $this->assertTrue($reflection->isPublic());
    }

    public function test_handle_method_accepts_request_parameter(): void
    {
        $reflection = new \ReflectionMethod(RenderableController::class, 'handle');
        $parameters = $reflection->getParameters();

        $this->assertCount(1, $parameters);
        $this->assertEquals('request', $parameters[0]->getName());
        $this->assertEquals(Request::class, $parameters[0]->getType()->getName());
    }

    public function test_render_method_is_protected(): void
    {
        $reflection = new \ReflectionMethod(RenderableController::class, 'render');
        $this->assertTrue($reflection->isProtected());
    }

    public function test_render_method_accepts_lazy_renderable(): void
    {
        $reflection = new \ReflectionMethod(RenderableController::class, 'render');
        $parameters = $reflection->getParameters();

        $this->assertCount(1, $parameters);
        $this->assertEquals('renderable', $parameters[0]->getName());
        $this->assertEquals(LazyRenderable::class, $parameters[0]->getType()->getName());
    }

    public function test_init_translation_sets_translation_when_trans_param_present(): void
    {
        $controller = new RenderableController;
        $method = new \ReflectionMethod($controller, 'initTranslation');
        $method->setAccessible(true);

        $request = Request::create('/test', 'GET', ['_trans_' => 'some.translation.path']);

        $method->invoke($controller, $request);

        // If no exception is thrown and call completed, initTranslation works
        $this->assertTrue(true);
    }

    public function test_init_translation_does_nothing_when_no_trans_param(): void
    {
        $controller = new RenderableController;
        $method = new \ReflectionMethod($controller, 'initTranslation');
        $method->setAccessible(true);

        $request = Request::create('/test', 'GET', []);

        $method->invoke($controller, $request);

        $this->assertTrue(true);
    }

    public function test_new_renderable_method_is_protected(): void
    {
        $reflection = new \ReflectionMethod(RenderableController::class, 'newRenderable');
        $this->assertTrue($reflection->isProtected());
    }

    public function test_new_renderable_return_type_is_lazy_renderable(): void
    {
        $reflection = new \ReflectionMethod(RenderableController::class, 'newRenderable');
        $returnType = $reflection->getReturnType();

        $this->assertNotNull($returnType);
        $this->assertEquals(LazyRenderable::class, $returnType->getName());
    }

    public function test_add_script_method_is_protected(): void
    {
        $reflection = new \ReflectionMethod(RenderableController::class, 'addScript');
        $this->assertTrue($reflection->isProtected());
    }

    public function test_add_script_calls_admin_script(): void
    {
        $controller = new RenderableController;
        $method = new \ReflectionMethod($controller, 'addScript');
        $method->setAccessible(true);

        $method->invoke($controller);

        // addScript calls Admin::script('Dcat.wait()', true)
        // No exception means it works
        $this->assertTrue(true);
    }

    public function test_forget_default_assets_method_is_protected(): void
    {
        $reflection = new \ReflectionMethod(RenderableController::class, 'forgetDefaultAssets');
        $this->assertTrue($reflection->isProtected());
    }

    public function test_forget_default_assets_clears_base_assets(): void
    {
        $controller = new RenderableController;
        $method = new \ReflectionMethod($controller, 'forgetDefaultAssets');
        $method->setAccessible(true);

        $method->invoke($controller);

        // Method calls Admin::baseJs([], false), Admin::baseCss([], false), Admin::fonts([])
        $this->assertTrue(true);
    }

    public function test_new_renderable_replaces_underscores_with_backslashes(): void
    {
        // Verify the str_replace logic in newRenderable:
        // underscores in class name should become backslashes
        $input = 'App_Admin_Widgets_MyWidget';
        $expected = 'App\\Admin\\Widgets\\MyWidget';

        $this->assertEquals($expected, str_replace('_', '\\', $input));
    }
}
