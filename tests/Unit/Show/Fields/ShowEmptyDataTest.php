<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Show\Fields;

use Dcat\Admin\Show\AbstractField;
use Dcat\Admin\Show\Fields\EmptyData;
use Dcat\Admin\Tests\TestCase;

class ShowEmptyDataTest extends TestCase
{
    protected function makeField(): EmptyData
    {
        return new EmptyData;
    }

    public function test_extends_abstract_field(): void
    {
        $field = $this->makeField();

        $this->assertInstanceOf(AbstractField::class, $field);
    }

    public function test_default_placeholder_is_dash(): void
    {
        $field = $this->makeField();

        $ref = new \ReflectionProperty($field, 'placeholder');
        $ref->setAccessible(true);

        $this->assertSame('-', $ref->getValue($field));
    }

    public function test_placeholder_setter_fluent(): void
    {
        $field = $this->makeField();
        $result = $field->placeholder('N/A');

        $this->assertSame($field, $result);

        $ref = new \ReflectionProperty($field, 'placeholder');
        $ref->setAccessible(true);
        $this->assertSame('N/A', $ref->getValue($field));
    }

    public function test_render_returns_value_when_present(): void
    {
        $field = $this->makeField();
        $field->setValue('actual value');

        $this->assertSame('actual value', $field->render());
    }

    public function test_render_returns_placeholder_when_value_is_empty_string(): void
    {
        $field = $this->makeField();
        $field->setValue('');

        $this->assertSame('-', $field->render());
    }

    public function test_render_returns_placeholder_when_value_is_null(): void
    {
        $field = $this->makeField();
        $field->setValue(null);

        $this->assertSame('-', $field->render());
    }

    public function test_render_returns_placeholder_when_value_is_zero(): void
    {
        // ?: 运算符对 0 视为 falsy
        $field = $this->makeField();
        $field->setValue(0);

        $this->assertSame('-', $field->render());
    }

    public function test_render_returns_custom_placeholder(): void
    {
        $field = $this->makeField();
        $field->placeholder('暂无数据');
        $field->setValue(null);

        $this->assertSame('暂无数据', $field->render());
    }
}
