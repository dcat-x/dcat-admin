<?php

namespace Dcat\Admin\Tests\Unit\Show;

use Dcat\Admin\Show\AbstractField;
use Dcat\Admin\Tests\TestCase;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Fluent;

class AbstractFieldTest extends TestCase
{
    /**
     * 创建匿名子类实例。
     */
    protected function makeField(): AbstractField
    {
        return new class extends AbstractField
        {
            public function render()
            {
                return $this->value;
            }
        };
    }

    public function test_implements_renderable_interface(): void
    {
        $field = $this->makeField();

        $this->assertInstanceOf(Renderable::class, $field);
    }

    public function test_default_border_is_true(): void
    {
        $field = $this->makeField();

        $this->assertTrue($field->border);
    }

    public function test_default_escape_is_false(): void
    {
        $field = $this->makeField();

        $this->assertFalse($field->escape);
    }

    public function test_set_value_stores_value_and_returns_self(): void
    {
        $field = $this->makeField();
        $result = $field->setValue('test_value');

        $this->assertSame($field, $result);

        $ref = new \ReflectionProperty($field, 'value');
        $ref->setAccessible(true);
        $this->assertSame('test_value', $ref->getValue($field));
    }

    public function test_set_model_stores_model_and_returns_self(): void
    {
        $field = $this->makeField();
        $model = new Fluent(['id' => 1, 'name' => 'test']);

        $result = $field->setModel($model);

        $this->assertSame($field, $result);

        $ref = new \ReflectionProperty($field, 'model');
        $ref->setAccessible(true);
        $this->assertSame($model, $ref->getValue($field));
    }

    public function test_render_returns_value_set_via_set_value(): void
    {
        $field = $this->makeField();
        $field->setValue('hello');

        $this->assertSame('hello', $field->render());
    }

    public function test_set_value_accepts_various_types(): void
    {
        $field = $this->makeField();

        $field->setValue(42);
        $ref = new \ReflectionProperty($field, 'value');
        $ref->setAccessible(true);
        $this->assertSame(42, $ref->getValue($field));

        $field->setValue(['a', 'b']);
        $this->assertSame(['a', 'b'], $ref->getValue($field));

        $field->setValue(null);
        $this->assertNull($ref->getValue($field));
    }
}
