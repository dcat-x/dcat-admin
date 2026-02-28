<?php

namespace Dcat\Admin\Tests\Unit\Form\Field;

use Dcat\Admin\Form\Field\Slider;
use Dcat\Admin\Tests\TestCase;
use Mockery;

class SliderTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    protected function createSlider(string $column = 'value', string $label = 'Value'): Slider
    {
        return new Slider($column, [$label]);
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

    public function test_default_options_type(): void
    {
        $slider = $this->createSlider();

        $options = $this->getProtectedProperty($slider, 'options');

        $this->assertSame('single', $options['type']);
    }

    public function test_default_options_prettify(): void
    {
        $slider = $this->createSlider();

        $options = $this->getProtectedProperty($slider, 'options');

        $this->assertFalse($options['prettify']);
    }

    public function test_default_options_has_grid(): void
    {
        $slider = $this->createSlider();

        $options = $this->getProtectedProperty($slider, 'options');

        $this->assertTrue($options['hasGrid']);
    }

    public function test_column_is_set(): void
    {
        $slider = $this->createSlider('brightness', 'Brightness');

        $this->assertSame('brightness', $slider->column());
    }

    public function test_label_is_set(): void
    {
        $slider = $this->createSlider('brightness', 'Brightness');

        $this->assertSame('Brightness', $slider->label());
    }

    // -------------------------------------------------------
    // options()
    // -------------------------------------------------------

    public function test_options_merges_with_defaults(): void
    {
        $slider = $this->createSlider();

        $result = $slider->options(['min' => 0, 'max' => 100]);

        $this->assertSame($slider, $result);
        $options = $this->getProtectedProperty($slider, 'options');
        $this->assertSame(0, $options['min']);
        $this->assertSame(100, $options['max']);
        // defaults should still be present
        $this->assertSame('single', $options['type']);
        $this->assertFalse($options['prettify']);
        $this->assertTrue($options['hasGrid']);
    }

    public function test_options_can_override_defaults(): void
    {
        $slider = $this->createSlider();

        $slider->options(['type' => 'double', 'prettify' => true]);

        $options = $this->getProtectedProperty($slider, 'options');
        $this->assertSame('double', $options['type']);
        $this->assertTrue($options['prettify']);
    }

    public function test_options_step_setting(): void
    {
        $slider = $this->createSlider();

        $slider->options(['step' => 5]);

        $options = $this->getProtectedProperty($slider, 'options');
        $this->assertSame(5, $options['step']);
    }

    // -------------------------------------------------------
    // Inherited Field functionality
    // -------------------------------------------------------

    public function test_default_value(): void
    {
        $slider = $this->createSlider();

        $result = $slider->default(50);

        $this->assertSame($slider, $result);
        $this->assertSame(50, $this->getProtectedProperty($slider, 'default'));
    }

    public function test_help_text(): void
    {
        $slider = $this->createSlider();

        $result = $slider->help('Drag to select value');

        $this->assertSame($slider, $result);
        $help = $this->getProtectedProperty($slider, 'help');
        $this->assertSame('Drag to select value', $help['text']);
    }

    public function test_width(): void
    {
        $slider = $this->createSlider();

        $result = $slider->width(10, 2);

        $this->assertSame($slider, $result);
        $width = $this->getProtectedProperty($slider, 'width');
        $this->assertSame(10, $width['field']);
        $this->assertSame(2, $width['label']);
    }

    public function test_disable(): void
    {
        $slider = $this->createSlider();

        $result = $slider->disable();

        $this->assertSame($slider, $result);
    }
}
