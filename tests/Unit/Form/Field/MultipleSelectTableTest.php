<?php

namespace Dcat\Admin\Tests\Unit\Form\Field;

use Dcat\Admin\Form\Field\MultipleSelectTable;
use Dcat\Admin\Form\Field\SelectTable;
use Dcat\Admin\Tests\TestCase;
use Mockery;

class MultipleSelectTableTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    // -------------------------------------------------------
    // Class structure
    // -------------------------------------------------------

    public function test_class_exists(): void
    {
        $this->assertTrue(class_exists(MultipleSelectTable::class));
    }

    public function test_is_subclass_of_select_table(): void
    {
        $this->assertTrue(is_subclass_of(MultipleSelectTable::class, SelectTable::class));
    }

    // -------------------------------------------------------
    // Default property values via reflection
    // -------------------------------------------------------

    public function test_view_default(): void
    {
        $ref = new \ReflectionProperty(MultipleSelectTable::class, 'view');
        $ref->setAccessible(true);

        $this->assertSame('admin::form.selecttable', $ref->getDefaultValue());
    }

    public function test_max_default_zero(): void
    {
        $ref = new \ReflectionProperty(MultipleSelectTable::class, 'max');
        $ref->setAccessible(true);

        $this->assertSame(0, $ref->getDefaultValue());
    }

    // -------------------------------------------------------
    // Method existence
    // -------------------------------------------------------

    public function test_method_max_exists(): void
    {
        $this->assertTrue(method_exists(MultipleSelectTable::class, 'max'));
    }

    public function test_method_prepare_input_value_exists(): void
    {
        $this->assertTrue(method_exists(MultipleSelectTable::class, 'prepareInputValue'));
    }

    public function test_method_render_exists(): void
    {
        $this->assertTrue(method_exists(MultipleSelectTable::class, 'render'));
    }
}
