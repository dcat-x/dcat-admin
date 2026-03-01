<?php

namespace Dcat\Admin\Tests\Unit\Grid\Concerns;

use Dcat\Admin\Grid;
use Dcat\Admin\Grid\Concerns\HasExporter;
use Dcat\Admin\Tests\TestCase;
use Mockery;

class HasExporterTest extends TestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }

    public function test_allow_exporter_returns_false_initially(): void
    {
        $helper = new HasExporterTestHelper;
        $this->assertFalse($helper->allowExporter());
    }

    public function test_enable_exporter_property_default_false(): void
    {
        $helper = new HasExporterTestHelper;
        $ref = new \ReflectionProperty($helper, 'enableExporter');
        $ref->setAccessible(true);
        $this->assertFalse($ref->getValue($helper));
    }

    public function test_exported_property_default_false(): void
    {
        $helper = new HasExporterTestHelper;
        $ref = new \ReflectionProperty($helper, 'exported');
        $ref->setAccessible(true);
        $this->assertFalse($ref->getValue($helper));
    }

    public function test_exporter_property_initially_null(): void
    {
        $helper = new HasExporterTestHelper;
        $ref = new \ReflectionProperty($helper, 'exporter');
        $ref->setAccessible(true);
        $this->assertNull($ref->getValue($helper));
    }

    public function test_render_export_button_returns_empty_when_not_allowed(): void
    {
        $helper = new HasExporterTestHelper;
        $this->assertSame('', $helper->renderExportButton());
    }

    public function test_export_method_exists(): void
    {
        $this->assertTrue(method_exists(HasExporterTestHelper::class, 'export'));
    }

    public function test_exporter_method_exists(): void
    {
        $this->assertTrue(method_exists(HasExporterTestHelper::class, 'exporter'));
    }

    public function test_handle_export_request_method_exists(): void
    {
        $this->assertTrue(method_exists(HasExporterTestHelper::class, 'handleExportRequest'));
    }

    public function test_export_url_method_exists(): void
    {
        $this->assertTrue(method_exists(HasExporterTestHelper::class, 'exportUrl'));
    }
}

class HasExporterTestHelper extends Grid
{
    use HasExporter;

    public function __construct()
    {
        // Skip parent constructor
    }
}
