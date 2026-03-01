<?php

namespace Dcat\Admin\Tests\Unit\Grid\Filter\Presenter;

use Dcat\Admin\Grid\Filter\Presenter\Toggle;
use Dcat\Admin\Tests\TestCase;
use ReflectionProperty;

class TogglePresenterTest extends TestCase
{
    protected function makeToggle($onText = null, $offText = null): Toggle
    {
        return new Toggle($onText, $offText);
    }

    protected function getProtectedProperty(object $object, string $property)
    {
        $reflection = new ReflectionProperty($object, $property);
        $reflection->setAccessible(true);

        return $reflection->getValue($object);
    }

    public function test_default_on_value_is_one(): void
    {
        $toggle = $this->makeToggle();

        $this->assertEquals(1, $this->getProtectedProperty($toggle, 'onValue'));
    }

    public function test_default_off_value_is_zero(): void
    {
        $toggle = $this->makeToggle();

        $this->assertEquals(0, $this->getProtectedProperty($toggle, 'offValue'));
    }

    public function test_default_size_is_small(): void
    {
        $toggle = $this->makeToggle();

        $this->assertEquals('small', $this->getProtectedProperty($toggle, 'size'));
    }

    public function test_values_setter_is_fluent(): void
    {
        $toggle = $this->makeToggle();

        $result = $toggle->values('yes', 'no');

        $this->assertSame($toggle, $result);
    }

    public function test_values_setter_updates_on_and_off_values(): void
    {
        $toggle = $this->makeToggle();

        $toggle->values('active', 'inactive');

        $this->assertEquals('active', $this->getProtectedProperty($toggle, 'onValue'));
        $this->assertEquals('inactive', $this->getProtectedProperty($toggle, 'offValue'));
    }

    public function test_text_setter_is_fluent(): void
    {
        $toggle = $this->makeToggle();

        $result = $toggle->text('Yes', 'No');

        $this->assertSame($toggle, $result);
    }

    public function test_text_setter_updates_on_and_off_text(): void
    {
        $toggle = $this->makeToggle();

        $toggle->text('Enabled', 'Disabled');

        $this->assertEquals('Enabled', $this->getProtectedProperty($toggle, 'onText'));
        $this->assertEquals('Disabled', $this->getProtectedProperty($toggle, 'offText'));
    }

    public function test_size_setter_is_fluent(): void
    {
        $toggle = $this->makeToggle();

        $result = $toggle->size('large');

        $this->assertSame($toggle, $result);
    }

    public function test_size_setter_updates_size(): void
    {
        $toggle = $this->makeToggle();

        $toggle->size('large');

        $this->assertEquals('large', $this->getProtectedProperty($toggle, 'size'));
    }

    public function test_default_variables_returns_correct_keys(): void
    {
        $toggle = $this->makeToggle();

        $vars = $toggle->defaultVariables();

        $this->assertArrayHasKey('onText', $vars);
        $this->assertArrayHasKey('offText', $vars);
        $this->assertArrayHasKey('onValue', $vars);
        $this->assertArrayHasKey('offValue', $vars);
        $this->assertArrayHasKey('size', $vars);
    }

    public function test_default_variables_returns_correct_values(): void
    {
        $toggle = $this->makeToggle('Yes', 'No');

        $toggle->values(1, 0)->size('small');

        $vars = $toggle->defaultVariables();

        $this->assertEquals('Yes', $vars['onText']);
        $this->assertEquals('No', $vars['offText']);
        $this->assertEquals(1, $vars['onValue']);
        $this->assertEquals(0, $vars['offValue']);
        $this->assertEquals('small', $vars['size']);
    }

    public function test_custom_text_reflected_in_default_variables(): void
    {
        $toggle = $this->makeToggle('On', 'Off');

        $vars = $toggle->defaultVariables();

        $this->assertEquals('On', $vars['onText']);
        $this->assertEquals('Off', $vars['offText']);
    }
}
