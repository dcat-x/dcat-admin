<?php

namespace Dcat\Admin\Tests\Unit\Grid\Displayers;

use Dcat\Admin\Grid\Displayers\AbstractDisplayer;
use Dcat\Admin\Grid\Displayers\Editable;
use Dcat\Admin\Tests\TestCase;
use Mockery;

class EditableTest extends TestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }

    public function test_class_exists(): void
    {
        $this->assertTrue(class_exists(Editable::class));
    }

    public function test_extends_abstract_displayer(): void
    {
        $this->assertTrue(is_subclass_of(Editable::class, AbstractDisplayer::class));
    }

    public function test_is_abstract_class(): void
    {
        $ref = new \ReflectionClass(Editable::class);

        $this->assertTrue($ref->isAbstract());
    }

    public function test_options_default_value(): void
    {
        $ref = new \ReflectionProperty(Editable::class, 'options');
        $ref->setAccessible(true);

        $this->assertSame(['refresh' => false], $ref->getDefaultValue());
    }

    public function test_has_method_display(): void
    {
        $this->assertTrue(method_exists(Editable::class, 'display'));
    }

    public function test_has_method_default_options(): void
    {
        $this->assertTrue(method_exists(Editable::class, 'defaultOptions'));
    }

    public function test_has_method_variables(): void
    {
        $this->assertTrue(method_exists(Editable::class, 'variables'));
    }

    public function test_has_method_get_name(): void
    {
        $this->assertTrue(method_exists(Editable::class, 'getName'));
    }

    public function test_has_method_get_value(): void
    {
        $this->assertTrue(method_exists(Editable::class, 'getValue'));
    }

    public function test_has_method_get_original(): void
    {
        $this->assertTrue(method_exists(Editable::class, 'getOriginal'));
    }

    public function test_has_method_get_selector(): void
    {
        $this->assertTrue(method_exists(Editable::class, 'getSelector'));
    }

    public function test_has_method_get_url(): void
    {
        $this->assertTrue(method_exists(Editable::class, 'getUrl'));
    }

    public function test_display_method_is_public(): void
    {
        $method = new \ReflectionMethod(Editable::class, 'display');

        $this->assertTrue($method->isPublic());
    }

    public function test_default_options_is_protected(): void
    {
        $method = new \ReflectionMethod(Editable::class, 'defaultOptions');

        $this->assertTrue($method->isProtected());
    }

    public function test_variables_is_public(): void
    {
        $method = new \ReflectionMethod(Editable::class, 'variables');

        $this->assertTrue($method->isPublic());
    }

    public function test_get_name_is_protected(): void
    {
        $method = new \ReflectionMethod(Editable::class, 'getName');

        $this->assertTrue($method->isProtected());
    }

    public function test_get_value_is_protected(): void
    {
        $method = new \ReflectionMethod(Editable::class, 'getValue');

        $this->assertTrue($method->isProtected());
    }

    public function test_get_original_is_protected(): void
    {
        $method = new \ReflectionMethod(Editable::class, 'getOriginal');

        $this->assertTrue($method->isProtected());
    }

    public function test_get_selector_is_protected(): void
    {
        $method = new \ReflectionMethod(Editable::class, 'getSelector');

        $this->assertTrue($method->isProtected());
    }

    public function test_get_url_is_protected(): void
    {
        $method = new \ReflectionMethod(Editable::class, 'getUrl');

        $this->assertTrue($method->isProtected());
    }

    public function test_type_property_exists(): void
    {
        $this->assertTrue(property_exists(Editable::class, 'type'));
    }

    public function test_view_property_exists(): void
    {
        $this->assertTrue(property_exists(Editable::class, 'view'));
    }
}
