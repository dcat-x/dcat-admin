<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Show\Fields;

use Dcat\Admin\Show\AbstractField;
use Dcat\Admin\Show\Fields\Fee;
use Dcat\Admin\Tests\TestCase;

class ShowFeeTest extends TestCase
{
    protected function makeField(): Fee
    {
        return new Fee;
    }

    public function test_extends_abstract_field(): void
    {
        $field = $this->makeField();

        $this->assertInstanceOf(AbstractField::class, $field);
    }

    public function test_default_symbol_is_dollar(): void
    {
        $field = $this->makeField();

        $ref = new \ReflectionProperty($field, 'symbol');
        $ref->setAccessible(true);

        $this->assertSame('$', $ref->getValue($field));
    }

    public function test_default_decimals_is_two(): void
    {
        $field = $this->makeField();

        $ref = new \ReflectionProperty($field, 'decimals');
        $ref->setAccessible(true);

        $this->assertSame(2, $ref->getValue($field));
    }

    public function test_symbol_setter_fluent(): void
    {
        $field = $this->makeField();
        $result = $field->symbol('¥');

        $this->assertSame($field, $result);

        $ref = new \ReflectionProperty($field, 'symbol');
        $ref->setAccessible(true);
        $this->assertSame('¥', $ref->getValue($field));
    }

    public function test_decimals_setter_fluent(): void
    {
        $field = $this->makeField();
        $result = $field->decimals(4);

        $this->assertSame($field, $result);

        $ref = new \ReflectionProperty($field, 'decimals');
        $ref->setAccessible(true);
        $this->assertSame(4, $ref->getValue($field));
    }

    public function test_render_returns_symbol_plus_formatted_amount(): void
    {
        $field = $this->makeField();
        $field->setValue(10000); // 100.00 元

        $result = $field->render();

        $this->assertSame('$100.00', $result);
    }

    public function test_render_with_null_value(): void
    {
        $field = $this->makeField();
        $field->setValue(null);

        $result = $field->render();

        $this->assertSame('$0.00', $result);
    }

    public function test_render_with_custom_symbol_and_decimals(): void
    {
        $field = $this->makeField();
        $field->symbol('¥')->decimals(2);
        $field->setValue(5050); // 50.50 元

        $result = $field->render();

        $this->assertSame('¥50.50', $result);
    }
}
