<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Show\Fields;

use Dcat\Admin\Show\AbstractField;
use Dcat\Admin\Show\Fields\Rate;
use Dcat\Admin\Tests\TestCase;

class ShowRateTest extends TestCase
{
    protected function makeField(): Rate
    {
        return new Rate;
    }

    public function test_extends_abstract_field(): void
    {
        $field = $this->makeField();

        $this->assertInstanceOf(AbstractField::class, $field);
    }

    public function test_render_returns_zero_percent_when_null(): void
    {
        $field = $this->makeField();
        $field->setValue(null);

        $this->assertSame('0%', $field->render());
    }

    public function test_render_returns_zero_percent_when_empty_string(): void
    {
        $field = $this->makeField();
        $field->setValue('');

        $this->assertSame('0%', $field->render());
    }

    public function test_render_returns_value_with_percent_suffix(): void
    {
        $field = $this->makeField();
        $field->setValue('85');

        $this->assertSame('85%', $field->render());
    }

    public function test_render_with_custom_suffix(): void
    {
        $field = $this->makeField();
        $field->setValue('50');

        $this->assertSame('50‰', $field->render('‰'));
    }

    public function test_render_with_decimals_applies_number_format(): void
    {
        $field = $this->makeField();
        $field->setValue('85.678');

        $this->assertSame('85.68%', $field->render('%', 2));
    }

    public function test_render_with_zero_decimals(): void
    {
        $field = $this->makeField();
        $field->setValue('85.678');

        $this->assertSame('86%', $field->render('%', 0));
    }

    public function test_render_with_integer_value(): void
    {
        $field = $this->makeField();
        $field->setValue(100);

        $this->assertSame('100%', $field->render());
    }
}
