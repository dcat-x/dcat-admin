<?php

namespace Dcat\Admin\Tests\Unit\Form\Field;

use Dcat\Admin\Form\Field;
use Dcat\Admin\Form\Field\SelectTable;
use Dcat\Admin\Tests\TestCase;
use Mockery;

class SelectTableTest extends TestCase
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
        $this->assertTrue(class_exists(SelectTable::class));
    }

    public function test_is_subclass_of_field(): void
    {
        $this->assertTrue(is_subclass_of(SelectTable::class, Field::class));
    }

    public function test_uses_can_load_fields_trait(): void
    {
        $traits = class_uses(SelectTable::class);

        $this->assertContains('Dcat\Admin\Form\Field\CanLoadFields', $traits);
    }

    public function test_uses_plain_input_trait(): void
    {
        $traits = class_uses(SelectTable::class);

        $this->assertContains('Dcat\Admin\Form\Field\PlainInput', $traits);
    }

    // -------------------------------------------------------
    // Default property values via reflection
    // -------------------------------------------------------

    public function test_style_default_primary(): void
    {
        $ref = new \ReflectionProperty(SelectTable::class, 'style');
        $ref->setAccessible(true);

        $this->assertSame('primary', $ref->getDefaultValue());
    }

    // -------------------------------------------------------
    // Method existence
    // -------------------------------------------------------

    public function test_method_title_exists(): void
    {
        $this->assertTrue(method_exists(SelectTable::class, 'title'));
    }

    public function test_method_dialog_width_exists(): void
    {
        $this->assertTrue(method_exists(SelectTable::class, 'dialogWidth'));
    }

    public function test_method_dialog_max_min_exists(): void
    {
        $this->assertTrue(method_exists(SelectTable::class, 'dialogMaxMin'));
    }

    public function test_method_dialog_resize_exists(): void
    {
        $this->assertTrue(method_exists(SelectTable::class, 'dialogResize'));
    }

    public function test_method_from_exists(): void
    {
        $this->assertTrue(method_exists(SelectTable::class, 'from'));
    }

    public function test_method_pluck_exists(): void
    {
        $this->assertTrue(method_exists(SelectTable::class, 'pluck'));
    }

    public function test_method_options_exists(): void
    {
        $this->assertTrue(method_exists(SelectTable::class, 'options'));
    }

    public function test_method_model_exists(): void
    {
        $this->assertTrue(method_exists(SelectTable::class, 'model'));
    }

    public function test_method_render_exists(): void
    {
        $this->assertTrue(method_exists(SelectTable::class, 'render'));
    }
}
