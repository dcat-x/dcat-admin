<?php

namespace Dcat\Admin\Tests\Unit\Grid\Displayers;

use Dcat\Admin\Grid\Displayers\AbstractDisplayer;
use Dcat\Admin\Grid\Displayers\Modal;
use Dcat\Admin\Tests\TestCase;
use Mockery;

class ModalTest extends TestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }

    public function test_class_exists(): void
    {
        $this->assertTrue(class_exists(Modal::class));
    }

    public function test_is_subclass_of_abstract_displayer(): void
    {
        $this->assertTrue(is_subclass_of(Modal::class, AbstractDisplayer::class));
    }

    public function test_xl_default_is_false(): void
    {
        $ref = new \ReflectionProperty(Modal::class, 'xl');
        $ref->setAccessible(true);

        $this->assertFalse($ref->getDefaultValue());
    }

    public function test_icon_default_is_fa_clone(): void
    {
        $ref = new \ReflectionProperty(Modal::class, 'icon');
        $ref->setAccessible(true);

        $this->assertSame('fa-clone', $ref->getDefaultValue());
    }

    public function test_method_title_exists(): void
    {
        $this->assertTrue(method_exists(Modal::class, 'title'));
    }

    public function test_method_xl_exists(): void
    {
        $this->assertTrue(method_exists(Modal::class, 'xl'));
    }

    public function test_method_icon_exists(): void
    {
        $this->assertTrue(method_exists(Modal::class, 'icon'));
    }

    public function test_method_display_exists(): void
    {
        $this->assertTrue(method_exists(Modal::class, 'display'));
    }

    public function test_method_set_up_lazy_renderable_exists(): void
    {
        $this->assertTrue(method_exists(Modal::class, 'setUpLazyRenderable'));
    }

    public function test_method_render_button_exists(): void
    {
        $this->assertTrue(method_exists(Modal::class, 'renderButton'));
    }

    public function test_title_property_default_is_null(): void
    {
        $ref = new \ReflectionProperty(Modal::class, 'title');
        $ref->setAccessible(true);

        $this->assertNull($ref->getDefaultValue());
    }

    public function test_title_method_is_public(): void
    {
        $ref = new \ReflectionMethod(Modal::class, 'title');

        $this->assertTrue($ref->isPublic());
    }

    public function test_xl_method_is_public(): void
    {
        $ref = new \ReflectionMethod(Modal::class, 'xl');

        $this->assertTrue($ref->isPublic());
    }

    public function test_icon_method_is_public(): void
    {
        $ref = new \ReflectionMethod(Modal::class, 'icon');

        $this->assertTrue($ref->isPublic());
    }

    public function test_set_up_lazy_renderable_is_protected(): void
    {
        $ref = new \ReflectionMethod(Modal::class, 'setUpLazyRenderable');

        $this->assertTrue($ref->isProtected());
    }

    public function test_render_button_is_protected(): void
    {
        $ref = new \ReflectionMethod(Modal::class, 'renderButton');

        $this->assertTrue($ref->isProtected());
    }

    public function test_display_method_is_public(): void
    {
        $ref = new \ReflectionMethod(Modal::class, 'display');

        $this->assertTrue($ref->isPublic());
    }

    public function test_title_method_accepts_string_parameter(): void
    {
        $ref = new \ReflectionMethod(Modal::class, 'title');
        $params = $ref->getParameters();

        $this->assertCount(1, $params);
        $this->assertSame('title', $params[0]->getName());
    }

    public function test_xl_method_has_no_parameters(): void
    {
        $ref = new \ReflectionMethod(Modal::class, 'xl');

        $this->assertCount(0, $ref->getParameters());
    }

    public function test_icon_method_accepts_one_parameter(): void
    {
        $ref = new \ReflectionMethod(Modal::class, 'icon');
        $params = $ref->getParameters();

        $this->assertCount(1, $params);
        $this->assertSame('icon', $params[0]->getName());
    }
}
