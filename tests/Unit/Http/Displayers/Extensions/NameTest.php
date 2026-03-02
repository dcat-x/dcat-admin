<?php

namespace Dcat\Admin\Tests\Unit\Http\Displayers\Extensions;

use Dcat\Admin\Grid\Displayers\AbstractDisplayer;
use Dcat\Admin\Http\Displayers\Extensions\Name;
use Dcat\Admin\Tests\TestCase;
use Mockery;

class NameTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    // -------------------------------------------------------
    // Class existence and inheritance
    // -------------------------------------------------------

    public function test_class_exists(): void
    {
        $this->assertTrue(class_exists(Name::class));
    }

    public function test_extends_abstract_displayer(): void
    {
        $this->assertTrue(is_subclass_of(Name::class, AbstractDisplayer::class));
    }

    // -------------------------------------------------------
    // Method existence
    // -------------------------------------------------------

    public function test_display_method_exists(): void
    {
        $this->assertTrue(method_exists(Name::class, 'display'));
    }

    public function test_resolve_action_method_exists(): void
    {
        $this->assertTrue(method_exists(Name::class, 'resolveAction'));
    }

    // -------------------------------------------------------
    // Method visibility
    // -------------------------------------------------------

    public function test_display_is_public(): void
    {
        $method = new \ReflectionMethod(Name::class, 'display');
        $this->assertTrue($method->isPublic());
    }

    public function test_resolve_action_is_protected(): void
    {
        $method = new \ReflectionMethod(Name::class, 'resolveAction');
        $this->assertTrue($method->isProtected());
    }

    // -------------------------------------------------------
    // Method parameters
    // -------------------------------------------------------

    public function test_display_has_no_parameters(): void
    {
        $method = new \ReflectionMethod(Name::class, 'display');
        $this->assertCount(0, $method->getParameters());
    }

    public function test_resolve_action_has_action_parameter(): void
    {
        $method = new \ReflectionMethod(Name::class, 'resolveAction');
        $params = $method->getParameters();

        $this->assertCount(1, $params);
        $this->assertSame('action', $params[0]->getName());
    }

    // -------------------------------------------------------
    // Class reflection
    // -------------------------------------------------------

    public function test_class_is_not_abstract(): void
    {
        $reflection = new \ReflectionClass(Name::class);
        $this->assertFalse($reflection->isAbstract());
    }

    public function test_class_declares_display_method(): void
    {
        $method = new \ReflectionMethod(Name::class, 'display');
        $this->assertSame(Name::class, $method->getDeclaringClass()->getName());
    }

    public function test_class_declares_resolve_action_method(): void
    {
        $method = new \ReflectionMethod(Name::class, 'resolveAction');
        $this->assertSame(Name::class, $method->getDeclaringClass()->getName());
    }

    public function test_class_has_expected_methods(): void
    {
        $reflection = new \ReflectionClass(Name::class);

        $this->assertTrue($reflection->hasMethod('display'));
        $this->assertTrue($reflection->hasMethod('resolveAction'));
    }
}
