<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Form\Field;

use Dcat\Admin\Form\Field;
use Dcat\Admin\Form\Field\Nullable;
use Dcat\Admin\Tests\TestCase;

class NullableTest extends TestCase
{
    public function test_is_instance_of_field(): void
    {
        $field = new Nullable;

        $this->assertInstanceOf(Field::class, $field);
    }

    public function test_constructor_requires_no_arguments(): void
    {
        $field = new Nullable;

        $this->assertInstanceOf(Nullable::class, $field);
    }

    public function test_call_returns_self_for_fluent_interface(): void
    {
        $field = new Nullable;

        $result = $field->someMethod();

        $this->assertSame($field, $result);
    }

    public function test_call_returns_self_for_any_method(): void
    {
        $field = new Nullable;

        $this->assertSame($field, $field->required());
        $this->assertSame($field, $field->rules('nullable'));
        $this->assertSame($field, $field->help('some help text'));
    }

    public function test_render_returns_null(): void
    {
        $field = new Nullable;

        $result = $field->render();

        $this->assertNull($result);
    }
}
