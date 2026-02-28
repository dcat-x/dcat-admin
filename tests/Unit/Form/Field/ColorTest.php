<?php

namespace Dcat\Admin\Tests\Unit\Form\Field;

use Dcat\Admin\Form\Field\Color;
use Dcat\Admin\Tests\TestCase;
use Mockery;

class ColorTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    protected function createColor(string $column = 'color', string $label = 'Color'): Color
    {
        return new Color($column, [$label]);
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

    public function test_view_is_set(): void
    {
        $color = $this->createColor();

        $view = $this->getProtectedProperty($color, 'view');

        $this->assertSame('admin::form.color', $view);
    }

    public function test_column_is_set(): void
    {
        $color = $this->createColor('bg_color', 'Background Color');

        $this->assertSame('bg_color', $color->column());
    }

    public function test_label_is_set(): void
    {
        $color = $this->createColor('bg_color', 'Background Color');

        $this->assertSame('Background Color', $color->label());
    }

    // -------------------------------------------------------
    // hex()
    // -------------------------------------------------------

    public function test_hex_sets_format_option(): void
    {
        $color = $this->createColor();

        $result = $color->hex();

        $this->assertSame($color, $result);
        $options = $this->getProtectedProperty($color, 'options');
        $this->assertSame('hex', $options['format']);
    }

    // -------------------------------------------------------
    // rgb()
    // -------------------------------------------------------

    public function test_rgb_sets_format_option(): void
    {
        $color = $this->createColor();

        $result = $color->rgb();

        $this->assertSame($color, $result);
        $options = $this->getProtectedProperty($color, 'options');
        $this->assertSame('rgb', $options['format']);
    }

    // -------------------------------------------------------
    // rgba()
    // -------------------------------------------------------

    public function test_rgba_sets_format_option(): void
    {
        $color = $this->createColor();

        $result = $color->rgba();

        $this->assertSame($color, $result);
        $options = $this->getProtectedProperty($color, 'options');
        $this->assertSame('rgba', $options['format']);
    }

    // -------------------------------------------------------
    // Format switching
    // -------------------------------------------------------

    public function test_format_can_be_switched(): void
    {
        $color = $this->createColor();

        $color->hex();
        $options = $this->getProtectedProperty($color, 'options');
        $this->assertSame('hex', $options['format']);

        $color->rgb();
        $options = $this->getProtectedProperty($color, 'options');
        $this->assertSame('rgb', $options['format']);

        $color->rgba();
        $options = $this->getProtectedProperty($color, 'options');
        $this->assertSame('rgba', $options['format']);
    }

    // -------------------------------------------------------
    // Chaining
    // -------------------------------------------------------

    public function test_hex_returns_self_for_chaining(): void
    {
        $color = $this->createColor();

        $result = $color->hex();

        $this->assertInstanceOf(Color::class, $result);
    }

    public function test_rgb_returns_self_for_chaining(): void
    {
        $color = $this->createColor();

        $result = $color->rgb();

        $this->assertInstanceOf(Color::class, $result);
    }

    public function test_rgba_returns_self_for_chaining(): void
    {
        $color = $this->createColor();

        $result = $color->rgba();

        $this->assertInstanceOf(Color::class, $result);
    }

    // -------------------------------------------------------
    // Inherited Text/Field functionality
    // -------------------------------------------------------

    public function test_default_value(): void
    {
        $color = $this->createColor();

        $result = $color->default('#ff0000');

        $this->assertSame($color, $result);
        $this->assertSame('#ff0000', $this->getProtectedProperty($color, 'default'));
    }

    public function test_help_text(): void
    {
        $color = $this->createColor();

        $result = $color->help('Select a color');

        $this->assertSame($color, $result);
        $help = $this->getProtectedProperty($color, 'help');
        $this->assertSame('Select a color', $help['text']);
    }
}
