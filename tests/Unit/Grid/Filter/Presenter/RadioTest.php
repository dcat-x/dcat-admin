<?php

namespace Dcat\Admin\Tests\Unit\Grid\Filter\Presenter;

use Dcat\Admin\Grid\Filter\AbstractFilter;
use Dcat\Admin\Grid\Filter\Presenter\Radio;
use Dcat\Admin\Tests\TestCase;
use Illuminate\Support\Collection;
use ReflectionProperty;

class RadioTest extends TestCase
{
    protected function makeRadio(array $options = []): Radio
    {
        return new Radio($options);
    }

    protected function attachFilter(Radio $radio): void
    {
        $filter = $this->createMock(AbstractFilter::class);
        $filter->method('column')->willReturn('status');

        $radio->setParent($filter);
    }

    public function test_constructor_sets_options_from_array(): void
    {
        $options = [1 => 'Active', 2 => 'Inactive'];
        $radio = $this->makeRadio($options);

        $ref = new ReflectionProperty($radio, 'options');
        $ref->setAccessible(true);

        $this->assertEquals($options, $ref->getValue($radio));
    }

    public function test_constructor_accepts_arrayable(): void
    {
        $collection = new Collection([1 => 'Yes', 0 => 'No']);
        $radio = new Radio($collection);

        $ref = new ReflectionProperty($radio, 'options');
        $ref->setAccessible(true);

        $this->assertEquals([1 => 'Yes', 0 => 'No'], $ref->getValue($radio));
    }

    public function test_constructor_with_empty_options(): void
    {
        $radio = $this->makeRadio();

        $ref = new ReflectionProperty($radio, 'options');
        $ref->setAccessible(true);

        $this->assertEquals([], $ref->getValue($radio));
    }

    public function test_inline_is_true_by_default(): void
    {
        $radio = $this->makeRadio();

        $ref = new ReflectionProperty($radio, 'inline');
        $ref->setAccessible(true);

        $this->assertTrue($ref->getValue($radio));
    }

    public function test_stacked_sets_inline_to_false(): void
    {
        $radio = $this->makeRadio();

        $result = $radio->stacked();

        $this->assertSame($radio, $result);

        $ref = new ReflectionProperty($radio, 'inline');
        $ref->setAccessible(true);

        $this->assertFalse($ref->getValue($radio));
    }

    public function test_show_label_sets_value(): void
    {
        $radio = $this->makeRadio();

        $result = $radio->showLabel(false);

        $this->assertSame($radio, $result);

        $ref = new ReflectionProperty($radio, 'showLabel');
        $ref->setAccessible(true);

        $this->assertFalse($ref->getValue($radio));
    }

    public function test_default_variables_returns_expected_data(): void
    {
        $options = ['a' => 'Alpha', 'b' => 'Beta'];
        $radio = $this->makeRadio($options);

        $vars = $radio->defaultVariables();

        $this->assertArrayHasKey('options', $vars);
        $this->assertArrayHasKey('inline', $vars);
        $this->assertArrayHasKey('showLabel', $vars);
        $this->assertEquals($options, $vars['options']);
        $this->assertTrue($vars['inline']);
        $this->assertTrue($vars['showLabel']);
    }

    public function test_default_variables_after_stacked(): void
    {
        $radio = $this->makeRadio([1 => 'One']);
        $radio->stacked();

        $vars = $radio->defaultVariables();

        $this->assertFalse($vars['inline']);
    }

    public function test_view_returns_radio_view(): void
    {
        $radio = $this->makeRadio();

        $this->assertEquals('admin::filter.radio', $radio->view());
    }
}
