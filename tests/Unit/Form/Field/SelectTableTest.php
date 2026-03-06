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

    public function test_chainable_configuration_methods_return_self(): void
    {
        $field = new SelectTable('user_id', 'User');

        $result = $field
            ->title('Select User')
            ->dialogWidth('70%')
            ->dialogMaxMin(true)
            ->dialogResize(true)
            ->pluck('name', 'id')
            ->options([1 => 'Tom']);

        $this->assertInstanceOf(Field::class, $field);
        $this->assertSame($field, $result);
    }

    public function test_model_sets_pluck_columns_and_options_callback(): void
    {
        $field = new SelectTable('user_id', 'User');
        $result = $field->model(\Dcat\Admin\Models\Administrator::class, 'id', 'name');

        $this->assertSame($field, $result);

        $visibleColumn = new \ReflectionProperty(SelectTable::class, 'visibleColumn');
        $visibleColumn->setAccessible(true);

        $key = new \ReflectionProperty(SelectTable::class, 'key');
        $key->setAccessible(true);

        $options = new \ReflectionProperty(Field::class, 'options');
        $options->setAccessible(true);

        $this->assertSame('name', $visibleColumn->getValue($field));
        $this->assertSame('id', $key->getValue($field));
        $this->assertInstanceOf(\Closure::class, $options->getValue($field));
    }
}
