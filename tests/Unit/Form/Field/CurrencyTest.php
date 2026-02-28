<?php

namespace Dcat\Admin\Tests\Unit\Form\Field;

use Dcat\Admin\Form\Field\Currency;
use Dcat\Admin\Tests\TestCase;

class CurrencyTest extends TestCase
{
    protected function getProtectedProperty(object $object, string $property)
    {
        $reflection = new \ReflectionProperty($object, $property);
        $reflection->setAccessible(true);

        return $reflection->getValue($object);
    }

    protected function createCurrency(string $column = 'price', string $label = 'Price'): Currency
    {
        return new Currency($column, [$label]);
    }

    // -------------------------------------------------------
    // symbol()
    // -------------------------------------------------------

    public function test_symbol_default_value(): void
    {
        $field = $this->createCurrency();

        $symbol = $this->getProtectedProperty($field, 'symbol');

        $this->assertSame('$', $symbol);
    }

    public function test_symbol_sets_value(): void
    {
        $field = $this->createCurrency();

        $result = $field->symbol('€');

        $this->assertSame($field, $result);
        $symbol = $this->getProtectedProperty($field, 'symbol');
        $this->assertSame('€', $symbol);
    }

    public function test_symbol_sets_yen(): void
    {
        $field = $this->createCurrency();

        $field->symbol('¥');

        $symbol = $this->getProtectedProperty($field, 'symbol');
        $this->assertSame('¥', $symbol);
    }

    // -------------------------------------------------------
    // digits()
    // -------------------------------------------------------

    public function test_digits_merges_options(): void
    {
        $field = $this->createCurrency();

        $result = $field->digits(4);

        $this->assertSame($field, $result);
        $options = $this->getProtectedProperty($field, 'options');
        $this->assertSame(4, $options['digits']);
    }

    public function test_digits_with_zero(): void
    {
        $field = $this->createCurrency();

        $field->digits(0);

        $options = $this->getProtectedProperty($field, 'options');
        $this->assertSame(0, $options['digits']);
    }

    // -------------------------------------------------------
    // prepareInputValue()
    // -------------------------------------------------------

    public function test_prepare_input_value_removes_commas(): void
    {
        $field = $this->createCurrency();

        $method = new \ReflectionMethod($field, 'prepareInputValue');
        $method->setAccessible(true);

        $this->assertSame('1000.00', $method->invoke($field, '1,000.00'));
        $this->assertSame('1000000', $method->invoke($field, '1,000,000'));
    }

    public function test_prepare_input_value_passes_non_string_through(): void
    {
        $field = $this->createCurrency();

        $method = new \ReflectionMethod($field, 'prepareInputValue');
        $method->setAccessible(true);

        $this->assertSame(100, $method->invoke($field, 100));
        $this->assertNull($method->invoke($field, null));
    }

    public function test_prepare_input_value_string_without_commas(): void
    {
        $field = $this->createCurrency();

        $method = new \ReflectionMethod($field, 'prepareInputValue');
        $method->setAccessible(true);

        $this->assertSame('500.50', $method->invoke($field, '500.50'));
    }

    // -------------------------------------------------------
    // default options
    // -------------------------------------------------------

    public function test_default_options(): void
    {
        $field = $this->createCurrency();

        $options = $this->getProtectedProperty($field, 'options');

        $this->assertSame('currency', $options['alias']);
        $this->assertSame('.', $options['radixPoint']);
        $this->assertSame('', $options['prefix']);
        $this->assertTrue($options['removeMaskOnSubmit']);
        $this->assertFalse($options['rightAlign']);
    }
}
