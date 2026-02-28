<?php

namespace Dcat\Admin\Tests\Unit\Form\Field;

use Dcat\Admin\Form\Field;
use Dcat\Admin\Form\Field\Divide;
use Dcat\Admin\Tests\TestCase;

class DivideTest extends TestCase
{
    // -------------------------------------------------------
    // instanceof & construction
    // -------------------------------------------------------

    public function test_is_instance_of_field(): void
    {
        $field = new Divide;

        $this->assertInstanceOf(Field::class, $field);
    }

    public function test_constructor_without_label(): void
    {
        $field = new Divide;

        $reflection = new \ReflectionProperty($field, 'label');
        $reflection->setAccessible(true);

        $this->assertNull($reflection->getValue($field));
    }

    public function test_constructor_with_label(): void
    {
        $field = new Divide('Section Title');

        $reflection = new \ReflectionProperty($field, 'label');
        $reflection->setAccessible(true);

        $this->assertSame('Section Title', $reflection->getValue($field));
    }

    // -------------------------------------------------------
    // render()
    // -------------------------------------------------------

    public function test_render_without_label_returns_hr(): void
    {
        $field = new Divide;

        $result = $field->render();

        $this->assertSame('<hr/>', $result);
    }

    public function test_render_with_label_returns_div_with_span(): void
    {
        $field = new Divide('My Section');

        $result = $field->render();

        $this->assertStringContainsString('form-divider', $result);
        $this->assertStringContainsString('<span>My Section</span>', $result);
        $this->assertStringContainsString('text-center', $result);
    }

    public function test_render_with_empty_string_label_returns_hr(): void
    {
        $field = new Divide('');

        $result = $field->render();

        $this->assertSame('<hr/>', $result);
    }
}
