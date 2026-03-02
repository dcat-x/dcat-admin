<?php

namespace Dcat\Admin\Tests\Unit\Grid\Displayers;

use Dcat\Admin\Grid\Displayers\AbstractDisplayer;
use Dcat\Admin\Grid\Displayers\DialogTree;
use Dcat\Admin\Tests\TestCase;
use Mockery;

class DialogTreeTest extends TestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }

    public function test_class_exists(): void
    {
        $this->assertTrue(class_exists(DialogTree::class));
    }

    public function test_extends_abstract_displayer(): void
    {
        $this->assertTrue(is_subclass_of(DialogTree::class, AbstractDisplayer::class));
    }

    public function test_area_default_value(): void
    {
        $ref = new \ReflectionProperty(DialogTree::class, 'area');
        $ref->setAccessible(true);

        $this->assertSame(['580px', '600px'], $ref->getDefaultValue());
    }

    public function test_column_names_default_value(): void
    {
        $ref = new \ReflectionProperty(DialogTree::class, 'columnNames');
        $ref->setAccessible(true);

        $expected = [
            'id' => 'id',
            'text' => 'name',
            'parent' => 'parent_id',
        ];

        $this->assertSame($expected, $ref->getDefaultValue());
    }

    public function test_nodes_default_empty_array(): void
    {
        $ref = new \ReflectionProperty(DialogTree::class, 'nodes');
        $ref->setAccessible(true);

        $this->assertSame([], $ref->getDefaultValue());
    }

    public function test_root_parent_id_default_value(): void
    {
        $ref = new \ReflectionProperty(DialogTree::class, 'rootParentId');
        $ref->setAccessible(true);

        $this->assertSame(0, $ref->getDefaultValue());
    }

    public function test_has_method_nodes(): void
    {
        $this->assertTrue(method_exists(DialogTree::class, 'nodes'));
    }

    public function test_has_method_root_parent_id(): void
    {
        $this->assertTrue(method_exists(DialogTree::class, 'rootParentId'));
    }

    public function test_has_method_url(): void
    {
        $this->assertTrue(method_exists(DialogTree::class, 'url'));
    }

    public function test_has_method_check_all(): void
    {
        $this->assertTrue(method_exists(DialogTree::class, 'checkAll'));
    }

    public function test_has_method_options(): void
    {
        $this->assertTrue(method_exists(DialogTree::class, 'options'));
    }

    public function test_has_method_title(): void
    {
        $this->assertTrue(method_exists(DialogTree::class, 'title'));
    }

    public function test_has_method_area(): void
    {
        $this->assertTrue(method_exists(DialogTree::class, 'area'));
    }

    public function test_has_method_set_id_column(): void
    {
        $this->assertTrue(method_exists(DialogTree::class, 'setIdColumn'));
    }

    public function test_has_method_set_title_column(): void
    {
        $this->assertTrue(method_exists(DialogTree::class, 'setTitleColumn'));
    }

    public function test_has_method_set_parent_column(): void
    {
        $this->assertTrue(method_exists(DialogTree::class, 'setParentColumn'));
    }

    public function test_has_method_display(): void
    {
        $this->assertTrue(method_exists(DialogTree::class, 'display'));
    }

    public function test_nodes_method_is_public(): void
    {
        $method = new \ReflectionMethod(DialogTree::class, 'nodes');

        $this->assertTrue($method->isPublic());
    }

    public function test_url_method_is_public(): void
    {
        $method = new \ReflectionMethod(DialogTree::class, 'url');

        $this->assertTrue($method->isPublic());
    }

    public function test_area_property_is_protected(): void
    {
        $ref = new \ReflectionProperty(DialogTree::class, 'area');

        $this->assertTrue($ref->isProtected());
    }

    public function test_column_names_property_is_protected(): void
    {
        $ref = new \ReflectionProperty(DialogTree::class, 'columnNames');

        $this->assertTrue($ref->isProtected());
    }
}
