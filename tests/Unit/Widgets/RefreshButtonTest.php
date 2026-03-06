<?php

namespace Dcat\Admin\Tests\Unit\Widgets;

use Dcat\Admin\Tests\TestCase;
use Dcat\Admin\Widgets\RefreshButton;

class RefreshButtonTest extends TestCase
{
    public function test_constructor(): void
    {
        $button = new RefreshButton;
        $this->assertInstanceOf(RefreshButton::class, $button);
    }

    public function test_default_icon(): void
    {
        $button = new RefreshButton;
        $vars = $button->defaultVariables();
        $this->assertEquals('feather icon-refresh-cw', $vars['icon']);
    }

    public function test_custom_icon(): void
    {
        $button = new RefreshButton;
        $result = $button->icon('fa fa-sync');
        $this->assertSame($button, $result);
        $vars = $button->defaultVariables();
        $this->assertEquals('fa fa-sync', $vars['icon']);
    }

    public function test_tooltip_default_null(): void
    {
        $button = new RefreshButton;
        $vars = $button->defaultVariables();
        $this->assertNull($vars['tooltip']);
    }

    public function test_tooltip_set(): void
    {
        $button = new RefreshButton;
        $result = $button->tooltip('Refresh Page');
        $this->assertSame($button, $result);
        $vars = $button->defaultVariables();
        $this->assertEquals('Refresh Page', $vars['tooltip']);
    }

    public function test_default_variables_has_parent_keys(): void
    {
        $button = new RefreshButton;
        $vars = $button->defaultVariables();
        $this->assertArrayContainsKeys(['attributes', 'icon', 'tooltip'], $vars);
    }

    private function assertArrayContainsKeys(array $expectedKeys, array $actual): void
    {
        $keys = array_keys($actual);

        foreach ($expectedKeys as $key) {
            $this->assertContains($key, $keys);
        }
    }
}
