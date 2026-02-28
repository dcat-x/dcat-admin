<?php

namespace Dcat\Admin\Tests\Unit\Grid\Tools;

use Dcat\Admin\Grid\Tools\RefreshButton;
use Dcat\Admin\Tests\TestCase;

class RefreshButtonTest extends TestCase
{
    protected function getProtectedProperty(object $object, string $property)
    {
        $reflection = new \ReflectionProperty($object, $property);
        $reflection->setAccessible(true);

        return $reflection->getValue($object);
    }

    // -------------------------------------------------------------------------
    // Constructor / defaults
    // -------------------------------------------------------------------------

    public function test_default_display_is_true(): void
    {
        $button = new RefreshButton;

        $this->assertTrue($this->getProtectedProperty($button, 'display'));
    }

    // -------------------------------------------------------------------------
    // display()
    // -------------------------------------------------------------------------

    public function test_display_sets_value_to_false(): void
    {
        $button = new RefreshButton;
        $button->display(false);

        $this->assertFalse($this->getProtectedProperty($button, 'display'));
    }

    public function test_display_sets_value_to_true(): void
    {
        $button = new RefreshButton;
        $button->display(false);
        $button->display(true);

        $this->assertTrue($this->getProtectedProperty($button, 'display'));
    }

    public function test_display_returns_this_for_fluent_api(): void
    {
        $button = new RefreshButton;
        $result = $button->display(true);

        $this->assertSame($button, $result);
    }

    // -------------------------------------------------------------------------
    // render()
    // -------------------------------------------------------------------------

    public function test_render_returns_html_when_display_is_true(): void
    {
        $button = new RefreshButton;
        $html = $button->render();

        $this->assertIsString($html);
        $this->assertStringContainsString('grid-refresh', $html);
        $this->assertStringContainsString('data-action="refresh"', $html);
    }

    public function test_render_returns_null_when_display_is_false(): void
    {
        $button = new RefreshButton;
        $button->display(false);

        $this->assertNull($button->render());
    }

    public function test_render_contains_refresh_icon(): void
    {
        $button = new RefreshButton;
        $html = $button->render();

        $this->assertStringContainsString('icon-refresh-cw', $html);
    }

    public function test_render_contains_button_classes(): void
    {
        $button = new RefreshButton;
        $html = $button->render();

        $this->assertStringContainsString('btn btn-secondary', $html);
        $this->assertStringContainsString('btn-mini', $html);
        $this->assertStringContainsString('btn-outline', $html);
    }

    // -------------------------------------------------------------------------
    // Interface
    // -------------------------------------------------------------------------

    public function test_implements_renderable(): void
    {
        $button = new RefreshButton;

        $this->assertInstanceOf(\Illuminate\Contracts\Support\Renderable::class, $button);
    }
}
