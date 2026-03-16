<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Widgets;

use Dcat\Admin\Tests\TestCase;
use Dcat\Admin\Widgets\Callout;

class CalloutTest extends TestCase
{
    public function test_creation(): void
    {
        $callout = new Callout('Content', 'Title');
        $this->assertInstanceOf(Callout::class, $callout);
    }

    public function test_content(): void
    {
        $callout = new Callout;
        $callout->content('My Content');
        $vars = $callout->defaultVariables();
        $this->assertSame('My Content', $vars['content']);
    }

    public function test_title(): void
    {
        $callout = new Callout;
        $callout->title('My Title');
        $vars = $callout->defaultVariables();
        $this->assertSame('My Title', $vars['title']);
    }

    public function test_info_style(): void
    {
        $callout = new Callout;
        $callout->info();
        $vars = $callout->defaultVariables();
        $this->assertStringContainsString('callout-info', $vars['attributes']);
    }

    public function test_success_style(): void
    {
        $callout = new Callout;
        $callout->success();
        $vars = $callout->defaultVariables();
        $this->assertStringContainsString('callout-success', $vars['attributes']);
    }

    public function test_warning_style(): void
    {
        $callout = new Callout;
        $callout->warning();
        $vars = $callout->defaultVariables();
        $this->assertStringContainsString('callout-warning', $vars['attributes']);
    }

    public function test_danger_style(): void
    {
        $callout = new Callout;
        $callout->danger();
        $vars = $callout->defaultVariables();
        $this->assertStringContainsString('callout-danger', $vars['attributes']);
    }

    public function test_primary_style(): void
    {
        $callout = new Callout;
        $callout->primary();
        $vars = $callout->defaultVariables();
        $this->assertStringContainsString('callout-primary', $vars['attributes']);
    }

    public function test_light_style(): void
    {
        $callout = new Callout;
        $callout->light();
        $vars = $callout->defaultVariables();
        $this->assertStringContainsString('callout-light', $vars['attributes']);
    }

    public function test_removable(): void
    {
        $callout = new Callout;
        $callout->removable();
        $vars = $callout->defaultVariables();
        $this->assertTrue($vars['showCloseBtn']);
    }

    public function test_removable_false(): void
    {
        $callout = new Callout;
        $callout->removable(false);
        $vars = $callout->defaultVariables();
        $this->assertFalse($vars['showCloseBtn']);
    }

    public function test_default_style_is_default(): void
    {
        $callout = new Callout('', null, 'default');
        $vars = $callout->defaultVariables();
        $this->assertStringContainsString('callout-default', $vars['attributes']);
    }
}
