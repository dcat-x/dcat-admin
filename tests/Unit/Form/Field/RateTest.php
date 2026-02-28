<?php

namespace Dcat\Admin\Tests\Unit\Form\Field;

use Dcat\Admin\Form\Field\Rate;
use Dcat\Admin\Tests\TestCase;
use Mockery;

class RateTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    protected function createRate(string $column = 'rate', string $label = 'Rate'): Rate
    {
        return new Rate($column, [$label]);
    }

    protected function getProtectedProperty(object $object, string $property)
    {
        $reflection = new \ReflectionProperty($object, $property);
        $reflection->setAccessible(true);

        return $reflection->getValue($object);
    }

    // -------------------------------------------------------
    // Construction & defaults
    // -------------------------------------------------------

    public function test_column_is_set(): void
    {
        $rate = $this->createRate('tax_rate', 'Tax Rate');

        $this->assertSame('tax_rate', $rate->column());
    }

    public function test_label_is_set(): void
    {
        $rate = $this->createRate('tax_rate', 'Tax Rate');

        $this->assertSame('Tax Rate', $rate->label());
    }

    // -------------------------------------------------------
    // Inherited Text/Field functionality
    // -------------------------------------------------------

    public function test_default_value(): void
    {
        $rate = $this->createRate();

        $result = $rate->default(50);

        $this->assertSame($rate, $result);
        $this->assertSame(50, $this->getProtectedProperty($rate, 'default'));
    }

    public function test_help_text(): void
    {
        $rate = $this->createRate();

        $result = $rate->help('Enter a percentage');

        $this->assertSame($rate, $result);
        $help = $this->getProtectedProperty($rate, 'help');
        $this->assertSame('Enter a percentage', $help['text']);
    }

    public function test_rules_can_be_set(): void
    {
        $rate = $this->createRate();

        $result = $rate->rules('required|numeric|min:0|max:100');

        $this->assertSame($rate, $result);
    }

    public function test_readonly_attribute(): void
    {
        $rate = $this->createRate();

        $result = $rate->readOnly();

        $this->assertSame($rate, $result);
    }

    public function test_disable(): void
    {
        $rate = $this->createRate();

        $result = $rate->disable();

        $this->assertSame($rate, $result);
    }

    public function test_width(): void
    {
        $rate = $this->createRate();

        $result = $rate->width(6, 3);

        $this->assertSame($rate, $result);
        $width = $this->getProtectedProperty($rate, 'width');
        $this->assertSame(6, $width['field']);
        $this->assertSame(3, $width['label']);
    }

    public function test_required_sets_validation(): void
    {
        $rate = $this->createRate();

        $result = $rate->required();

        $this->assertSame($rate, $result);
    }

    public function test_placeholder(): void
    {
        $rate = $this->createRate();

        $rate->placeholder('Enter rate');
        $value = $rate->placeholder();

        $this->assertSame('Enter rate', $value);
    }
}
