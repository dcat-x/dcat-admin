<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Form\Field;

use Dcat\Admin\Form\Field\Fee;
use Dcat\Admin\Tests\TestCase;
use Mockery;

class FeeTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    protected function getProtectedProperty(object $object, string $property)
    {
        $reflection = new \ReflectionProperty($object, $property);
        $reflection->setAccessible(true);

        return $reflection->getValue($object);
    }

    protected function invokeProtectedMethod(object $object, string $method, array $arguments = [])
    {
        $reflection = new \ReflectionMethod($object, $method);
        $reflection->setAccessible(true);

        return $reflection->invokeArgs($object, $arguments);
    }

    public function test_inputmask_updates_script_and_returns_self(): void
    {
        $field = new Fee('amount', ['Amount']);

        $result = $field->inputmask(['alias' => 'currency']);

        $this->assertSame($field, $result);

        $script = $this->getProtectedProperty($field, 'script');
        $this->assertStringContainsString('inputmask', $script);
        $this->assertStringContainsString('onBeforeMask', $script);
        $this->assertStringContainsString('onUnMask', $script);
        $this->assertStringContainsString('groupSeparator', $script);
    }

    public function test_inputmask_has_options_parameter_and_static_return_type(): void
    {
        $method = new \ReflectionMethod(Fee::class, 'inputmask');
        $params = $method->getParameters();

        $this->assertCount(1, $params);
        $this->assertSame('options', $params[0]->getName());

        $returnType = $method->getReturnType();
        $this->assertNotNull($returnType);
        $this->assertSame('static', $returnType->getName());
    }

    public function test_symbol_sets_currency_prefix_and_returns_self(): void
    {
        $field = new Fee('amount', ['Amount']);

        $result = $field->symbol('¥');

        $this->assertSame($field, $result);
        $this->assertSame('¥', $this->getProtectedProperty($field, 'symbol'));
    }

    public function test_digits_merges_options_and_returns_self(): void
    {
        $field = new Fee('amount', ['Amount']);

        $result = $field->digits(4);

        $this->assertSame($field, $result);

        $options = $this->getProtectedProperty($field, 'options');
        $this->assertSame(4, $options['digits']);
    }

    public function test_prepare_input_value_removes_commas_in_string_values(): void
    {
        $field = new Fee('amount', ['Amount']);

        $prepared = $this->invokeProtectedMethod($field, 'prepareInputValue', ['1,234,567.89']);

        $this->assertSame('1234567.89', $prepared);
    }

    public function test_prepare_input_value_keeps_non_string_values_unchanged(): void
    {
        $field = new Fee('amount', ['Amount']);

        $prepared = $this->invokeProtectedMethod($field, 'prepareInputValue', [12345]);

        $this->assertSame(12345, $prepared);
    }
}
