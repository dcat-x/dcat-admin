<?php

namespace Dcat\Admin\Tests\Unit\Widgets;

use Dcat\Admin\Tests\TestCase;
use Dcat\Admin\Widgets\Alert;

class AlertTest extends TestCase
{
    public function test_alert_creation(): void
    {
        $alert = new Alert('Test content', 'Test Title');
        $this->assertInstanceOf(Alert::class, $alert);
    }

    public function test_alert_content(): void
    {
        $alert = new Alert;
        $alert->content('Alert content here');

        $variables = $alert->defaultVariables();
        $this->assertEquals('Alert content here', $variables['content']);
    }

    public function test_alert_title(): void
    {
        $alert = new Alert;
        $alert->title('Alert Title');

        $variables = $alert->defaultVariables();
        $this->assertEquals('Alert Title', $variables['title']);
    }

    public function test_alert_style(): void
    {
        $alert = new Alert('Content', 'Title', 'warning');

        $variables = $alert->defaultVariables();
        $this->assertStringContainsString('alert-warning', $variables['attributes']);
    }

    public function test_alert_info_style(): void
    {
        $alert = new Alert;
        $alert->info();

        $variables = $alert->defaultVariables();
        $this->assertStringContainsString('alert-info', $variables['attributes']);
        $this->assertEquals('fa fa-info', $variables['icon']);
    }

    public function test_alert_success_style(): void
    {
        $alert = new Alert;
        $alert->success();

        $variables = $alert->defaultVariables();
        $this->assertStringContainsString('alert-success', $variables['attributes']);
        $this->assertEquals('fa fa-check', $variables['icon']);
    }

    public function test_alert_warning_style(): void
    {
        $alert = new Alert;
        $alert->warning();

        $variables = $alert->defaultVariables();
        $this->assertStringContainsString('alert-warning', $variables['attributes']);
        $this->assertEquals('fa fa-warning', $variables['icon']);
    }

    public function test_alert_danger_style(): void
    {
        $alert = new Alert;
        $alert->danger();

        $variables = $alert->defaultVariables();
        $this->assertStringContainsString('alert-danger', $variables['attributes']);
        $this->assertEquals('fa fa-ban', $variables['icon']);
    }

    public function test_alert_primary_style(): void
    {
        $alert = new Alert;
        $alert->primary();

        $variables = $alert->defaultVariables();
        $this->assertStringContainsString('alert-primary', $variables['attributes']);
    }

    public function test_alert_icon(): void
    {
        $alert = new Alert;
        $alert->icon('fa fa-custom');

        $variables = $alert->defaultVariables();
        $this->assertEquals('fa fa-custom', $variables['icon']);
    }

    public function test_alert_removable(): void
    {
        $alert = new Alert;
        $alert->removable();

        $variables = $alert->defaultVariables();
        $this->assertTrue($variables['showCloseBtn']);
    }

    public function test_alert_removable_disabled(): void
    {
        $alert = new Alert;
        $alert->removable(false);

        $variables = $alert->defaultVariables();
        $this->assertFalse($variables['showCloseBtn']);
    }

    public function test_alert_static_make(): void
    {
        $alert = Alert::make('Content', 'Title');
        $this->assertInstanceOf(Alert::class, $alert);
    }

    public function test_alert_chaining(): void
    {
        $alert = (new Alert)
            ->title('Chained Title')
            ->content('Chained Content')
            ->success()
            ->icon('fa fa-custom')
            ->removable();

        $variables = $alert->defaultVariables();
        $this->assertEquals('Chained Title', $variables['title']);
        $this->assertEquals('Chained Content', $variables['content']);
        $this->assertEquals('fa fa-custom', $variables['icon']);
        $this->assertTrue($variables['showCloseBtn']);
    }
}
