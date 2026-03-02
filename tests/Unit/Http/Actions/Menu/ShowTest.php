<?php

namespace Dcat\Admin\Tests\Unit\Http\Actions\Menu;

use Dcat\Admin\Http\Actions\Menu\Show;
use Dcat\Admin\Tests\TestCase;
use Dcat\Admin\Tree\RowAction;
use Mockery;

class ShowTest extends TestCase
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
        $this->assertTrue(class_exists(Show::class));
    }

    public function test_extends_row_action(): void
    {
        $this->assertTrue(is_subclass_of(Show::class, RowAction::class));
    }

    // -------------------------------------------------------
    // Method existence
    // -------------------------------------------------------

    public function test_handle_method_exists(): void
    {
        $this->assertTrue(method_exists(Show::class, 'handle'));
    }

    public function test_title_method_exists(): void
    {
        $this->assertTrue(method_exists(Show::class, 'title'));
    }

    // -------------------------------------------------------
    // Method visibility
    // -------------------------------------------------------

    public function test_handle_is_public(): void
    {
        $method = new \ReflectionMethod(Show::class, 'handle');
        $this->assertTrue($method->isPublic());
    }

    public function test_title_is_public(): void
    {
        $method = new \ReflectionMethod(Show::class, 'title');
        $this->assertTrue($method->isPublic());
    }

    // -------------------------------------------------------
    // Method parameters
    // -------------------------------------------------------

    public function test_handle_has_no_parameters(): void
    {
        $method = new \ReflectionMethod(Show::class, 'handle');
        $this->assertCount(0, $method->getParameters());
    }

    public function test_title_has_no_parameters(): void
    {
        $method = new \ReflectionMethod(Show::class, 'title');
        $this->assertCount(0, $method->getParameters());
    }

    // -------------------------------------------------------
    // Class reflection
    // -------------------------------------------------------

    public function test_class_is_not_abstract(): void
    {
        $reflection = new \ReflectionClass(Show::class);
        $this->assertFalse($reflection->isAbstract());
    }

    public function test_class_declares_handle_method(): void
    {
        $method = new \ReflectionMethod(Show::class, 'handle');
        $this->assertSame(Show::class, $method->getDeclaringClass()->getName());
    }

    public function test_class_declares_title_method(): void
    {
        $method = new \ReflectionMethod(Show::class, 'title');
        $this->assertSame(Show::class, $method->getDeclaringClass()->getName());
    }
}
