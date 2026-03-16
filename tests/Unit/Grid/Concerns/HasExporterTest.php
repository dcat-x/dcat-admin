<?php

declare(strict_types=1);

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

    public function test_export_method_signature_accepts_optional_exporter_driver(): void
    {
        $method = new \ReflectionMethod(HasExporterTestHelper::class, 'export');
        $params = $method->getParameters();

        $this->assertCount(1, $params);
        $this->assertSame('exporterDriver', $params[0]->getName());
        $this->assertTrue($params[0]->isDefaultValueAvailable());
        $this->assertNull($params[0]->getDefaultValue());
    }

    public function test_exporter_method_signature_has_no_parameters(): void
    {
        $method = new \ReflectionMethod(HasExporterTestHelper::class, 'exporter');

        $this->assertTrue($method->isPublic());
        $this->assertCount(0, $method->getParameters());
    }

    public function test_handle_export_request_signature_accepts_force_export_flag(): void
    {
        $method = new \ReflectionMethod(HasExporterTestHelper::class, 'handleExportRequest');
        $params = $method->getParameters();

        $this->assertCount(1, $params);
        $this->assertSame('forceExport', $params[0]->getName());
        $this->assertTrue($params[0]->isDefaultValueAvailable());
        $this->assertFalse($params[0]->getDefaultValue());
    }

    public function test_export_url_signature_accepts_scope_and_args(): void
    {
        $method = new \ReflectionMethod(HasExporterTestHelper::class, 'exportUrl');
        $params = $method->getParameters();

        $this->assertCount(2, $params);
        $this->assertSame('scope', $params[0]->getName());
        $this->assertSame('args', $params[1]->getName());
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
