<?php

namespace Dcat\Admin\Tests\Unit;

use Dcat\Admin\Color;
use Dcat\Admin\Tests\TestCase;

class ColorTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->app['config']->set('admin.layout.color', 'default');
    }

    protected function createColor(): Color
    {
        $color = new Color;
        $color->setName('default');

        return $color;
    }

    public function test_default_color_constant(): void
    {
        $this->assertEquals('default', Color::DEFAULT_COLOR);
    }

    public function test_get_name_returns_default(): void
    {
        $this->app['config']->set('admin.layout.color', null);
        $color = new Color;

        $this->assertEquals('default', $color->getName());
    }

    public function test_get_name_from_config(): void
    {
        $this->app['config']->set('admin.layout.color', 'blue');
        $color = new Color;

        $this->assertEquals('blue', $color->getName());
    }

    public function test_set_name(): void
    {
        $color = new Color;
        $color->setName('green');

        $this->assertEquals('green', $color->getName());
    }

    public function test_get_returns_color_value(): void
    {
        $color = $this->createColor();
        $result = $color->get('dark');

        $this->assertEquals('#22292f', $result);
    }

    public function test_get_returns_default_for_missing(): void
    {
        $color = $this->createColor();
        $result = $color->get('nonexistent', '#000');

        $this->assertEquals('#000', $result);
    }

    public function test_get_returns_null_for_missing_without_default(): void
    {
        $color = $this->createColor();
        $result = $color->get('nonexistent');

        $this->assertNull($result);
    }

    public function test_get_resolves_alias(): void
    {
        $color = $this->createColor();
        // info -> blue -> '#3085d6'
        $result = $color->get('info');

        $this->assertEquals('#3085d6', $result);
    }

    public function test_get_resolves_primary_from_extension(): void
    {
        $color = new Color;
        $color->setName('default');
        $result = $color->get('primary');

        $this->assertEquals('#586cb1', $result);
    }

    public function test_all_returns_array(): void
    {
        $color = $this->createColor();
        $result = $color->all();

        $this->assertIsArray($result);
    }

    public function test_all_resolves_aliases(): void
    {
        $color = $this->createColor();
        $all = $color->all();

        // 'info' alias should be resolved to the actual color value, not 'blue'
        $this->assertEquals('#3085d6', $all['info']);
    }

    public function test_all_caches_result(): void
    {
        $color = $this->createColor();
        $first = $color->all();
        $second = $color->all();

        $this->assertSame($first, $second);
    }

    public function test_lighten_returns_string(): void
    {
        $color = $this->createColor();
        $result = $color->lighten('primary', 10);

        $this->assertIsString($result);
    }

    public function test_darken_returns_string(): void
    {
        $color = $this->createColor();
        $result = $color->darken('primary', 10);

        $this->assertIsString($result);
    }

    public function test_alpha_returns_string(): void
    {
        $color = $this->createColor();
        $result = $color->alpha('primary', 0.5);

        $this->assertIsString($result);
    }

    public function test_magic_call_converts_method_to_slug(): void
    {
        $color = $this->createColor();
        // primaryDarker() -> darken('primary-darker', 0)
        $result = $color->primaryDarker();

        $this->assertIsString($result);
        // primary-darker in default theme is '#4c60a3'
        $this->assertEquals('#4c60a3', $result);
    }

    public function test_magic_call_with_amount(): void
    {
        $color = $this->createColor();
        // primary(10) -> darken('primary', 10)
        $result = $color->primary(10);

        $this->assertIsString($result);
        // Should be a darkened version, different from the original
        $this->assertNotEquals('#586cb1', $result);
    }

    public function test_extend_adds_new_theme(): void
    {
        Color::extend('custom-test-theme', ['primary' => '#ff0000']);

        $ref = new \ReflectionProperty(Color::class, 'extensions');
        $ref->setAccessible(true);
        $extensions = $ref->getValue();

        $this->assertArrayHasKey('custom-test-theme', $extensions);
        $this->assertEquals('#ff0000', $extensions['custom-test-theme']['colors']['primary']);
    }

    public function test_extend_theme_colors_used_when_selected(): void
    {
        Color::extend('custom-test-selected', ['primary' => '#abcdef']);

        $color = new Color;
        $color->setName('custom-test-selected');
        $result = $color->get('primary');

        $this->assertEquals('#abcdef', $result);
    }

    public function test_get_colors_merges_extension(): void
    {
        $color = new Color;
        $color->setName('default');

        // default theme overrides 'primary' from allColors
        $result = $color->get('primary');
        $this->assertEquals('#586cb1', $result);

        // But standard colors from allColors should still be available
        $dark = $color->get('dark');
        $this->assertEquals('#22292f', $dark);
    }

    public function test_default_extension_has_primary(): void
    {
        $ref = new \ReflectionProperty(Color::class, 'extensions');
        $ref->setAccessible(true);
        $extensions = $ref->getValue();

        $this->assertArrayHasKey('primary', $extensions['default']['colors']);
    }

    public function test_blue_light_extension_exists(): void
    {
        $ref = new \ReflectionProperty(Color::class, 'extensions');
        $ref->setAccessible(true);
        $extensions = $ref->getValue();

        $this->assertArrayHasKey('blue-light', $extensions);
    }

    public function test_green_extension_exists(): void
    {
        $ref = new \ReflectionProperty(Color::class, 'extensions');
        $ref->setAccessible(true);
        $extensions = $ref->getValue();

        $this->assertArrayHasKey('green', $extensions);
    }

    public function test_all_colors_has_standard_colors(): void
    {
        $ref = new \ReflectionProperty(Color::class, 'allColors');
        $ref->setAccessible(true);
        $allColors = $ref->getValue();

        $this->assertArrayHasKey('info', $allColors);
        $this->assertArrayHasKey('success', $allColors);
        $this->assertArrayHasKey('danger', $allColors);
        $this->assertArrayHasKey('warning', $allColors);
    }

    public function test_darken_with_zero_amount(): void
    {
        $color = $this->createColor();
        $result = $color->darken('primary', 0);

        // darken with 0 should return the original color
        $this->assertEquals('#586cb1', $result);
    }
}
