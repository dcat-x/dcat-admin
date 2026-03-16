<?php

declare(strict_types=1);

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

    public function test_it_is_instance_of_select_table(): void
    {
        $field = new MultipleSelectTable('user_ids', ['Users']);

        $this->assertInstanceOf(SelectTable::class, $field);
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

    public function test_max_updates_property_and_returns_self(): void
    {
        $field = new MultipleSelectTable('user_ids', ['Users']);

        $result = $field->max(10);

        $ref = new \ReflectionProperty(MultipleSelectTable::class, 'max');
        $ref->setAccessible(true);

        $this->assertSame($field, $result);
        $this->assertSame(10, $ref->getValue($field));
    }

    public function test_prepare_input_value_converts_to_array(): void
    {
        $field = new MultipleSelectTable('user_ids', ['Users']);
        $method = new \ReflectionMethod(MultipleSelectTable::class, 'prepareInputValue');
        $method->setAccessible(true);

        $single = $method->invoke($field, '5');
        $multiple = $method->invoke($field, ['5', '6']);

        $this->assertSame(['5'], $single);
        $this->assertSame(['5', '6'], $multiple);
    }

    public function test_render_method_signature_has_no_parameters(): void
    {
        $method = new \ReflectionMethod(MultipleSelectTable::class, 'render');

        $this->assertTrue($method->isPublic());
        $this->assertCount(0, $method->getParameters());
    }
}
