<?php

namespace Dcat\Admin\Tests\Unit\Grid\Displayers;

use Dcat\Admin\Grid\Displayers\AbstractDisplayer;
use Dcat\Admin\Grid\Displayers\Tree;
use Dcat\Admin\Tests\TestCase;
use Mockery;

class TreeTest extends TestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }

    public function test_class_exists(): void
    {
        $this->assertTrue(class_exists(Tree::class));
    }

    public function test_is_subclass_of_abstract_displayer(): void
    {
        $this->assertTrue(is_subclass_of(Tree::class, AbstractDisplayer::class));
    }

    public function test_static_js_contains_grid_extension(): void
    {
        $ref = new \ReflectionProperty(Tree::class, 'js');
        $ref->setAccessible(true);

        $js = $ref->getDefaultValue();

        $this->assertIsArray($js);
        $this->assertContains('@grid-extension', $js);
    }

    public function test_method_setup_script_exists(): void
    {
        $this->assertTrue(method_exists(Tree::class, 'setupScript'));
    }

    public function test_method_display_exists(): void
    {
        $this->assertTrue(method_exists(Tree::class, 'display'));
    }

    public function test_method_show_next_page_exists(): void
    {
        $this->assertTrue(method_exists(Tree::class, 'showNextPage'));
    }

    public function test_setup_script_is_protected(): void
    {
        $ref = new \ReflectionMethod(Tree::class, 'setupScript');

        $this->assertTrue($ref->isProtected());
    }

    public function test_display_is_public(): void
    {
        $ref = new \ReflectionMethod(Tree::class, 'display');

        $this->assertTrue($ref->isPublic());
    }

    public function test_show_next_page_is_protected(): void
    {
        $ref = new \ReflectionMethod(Tree::class, 'showNextPage');

        $this->assertTrue($ref->isProtected());
    }

    public function test_js_property_is_static(): void
    {
        $ref = new \ReflectionProperty(Tree::class, 'js');

        $this->assertTrue($ref->isStatic());
    }

    public function test_js_property_is_protected(): void
    {
        $ref = new \ReflectionProperty(Tree::class, 'js');

        $this->assertTrue($ref->isProtected());
    }

    public function test_display_method_has_no_parameters(): void
    {
        $ref = new \ReflectionMethod(Tree::class, 'display');

        $this->assertCount(0, $ref->getParameters());
    }

    public function test_setup_script_has_no_parameters(): void
    {
        $ref = new \ReflectionMethod(Tree::class, 'setupScript');

        $this->assertCount(0, $ref->getParameters());
    }

    public function test_show_next_page_has_no_parameters(): void
    {
        $ref = new \ReflectionMethod(Tree::class, 'showNextPage');

        $this->assertCount(0, $ref->getParameters());
    }
}
