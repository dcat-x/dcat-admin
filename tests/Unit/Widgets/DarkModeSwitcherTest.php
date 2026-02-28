<?php

namespace Dcat\Admin\Tests\Unit\Widgets;

use Dcat\Admin\Tests\TestCase;
use Dcat\Admin\Widgets\DarkModeSwitcher;

class DarkModeSwitcherTest extends TestCase
{
    public function test_constructor_with_true(): void
    {
        $switcher = new DarkModeSwitcher(true);
        $this->assertTrue($switcher->defaultDarkMode);
    }

    public function test_constructor_with_false(): void
    {
        $switcher = new DarkModeSwitcher(false);
        $this->assertFalse($switcher->defaultDarkMode);
    }

    public function test_render_contains_dark_mode_switcher_class(): void
    {
        $switcher = new DarkModeSwitcher(false);
        $html = $switcher->render();
        $this->assertStringContainsString('dark-mode-switcher', $html);
    }

    public function test_render_shows_moon_icon_when_not_dark(): void
    {
        $switcher = new DarkModeSwitcher(false);
        $html = $switcher->render();
        $this->assertStringContainsString('icon-moon', $html);
    }

    public function test_render_shows_sun_icon_when_dark(): void
    {
        $switcher = new DarkModeSwitcher(true);
        $html = $switcher->render();
        $this->assertStringContainsString('icon-sun', $html);
    }

    public function test_render_contains_script(): void
    {
        $switcher = new DarkModeSwitcher(false);
        $html = $switcher->render();
        $this->assertStringContainsString('Dcat.darkMode.initSwitcher', $html);
    }
}
