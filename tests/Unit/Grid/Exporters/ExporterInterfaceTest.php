<?php

namespace Dcat\Admin\Tests\Unit\Grid\Exporters;

use Dcat\Admin\Grid\Exporters\ExporterInterface;
use Dcat\Admin\Tests\TestCase;
use Mockery;

class ExporterInterfaceTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_interface_exists(): void
    {
        $this->assertTrue(interface_exists(ExporterInterface::class));
    }

    public function test_anonymous_class_implements_interface(): void
    {
        $instance = $this->makeExporter();

        $this->assertInstanceOf(ExporterInterface::class, $instance);
    }

    public function test_export_method_is_defined(): void
    {
        $reflection = new \ReflectionClass(ExporterInterface::class);

        $this->assertTrue($reflection->hasMethod('export'));
    }

    public function test_interface_has_exactly_one_method(): void
    {
        $reflection = new \ReflectionClass(ExporterInterface::class);

        $this->assertCount(1, $reflection->getMethods());
    }

    public function test_export_method_has_no_parameters(): void
    {
        $reflection = new \ReflectionMethod(ExporterInterface::class, 'export');

        $this->assertCount(0, $reflection->getParameters());
    }

    public function test_export_returns_value(): void
    {
        $instance = $this->makeExporter();

        $result = $instance->export();

        $this->assertSame('exported_data', $result);
    }

    public function test_exporter_interface_is_in_correct_namespace(): void
    {
        $reflection = new \ReflectionClass(ExporterInterface::class);

        $this->assertSame('Dcat\Admin\Grid\Exporters\ExporterInterface', $reflection->getName());
        $this->assertSame('Dcat\Admin\Grid\Exporters', $reflection->getNamespaceName());
    }

    public function test_exporter_is_interface_not_class(): void
    {
        $reflection = new \ReflectionClass(ExporterInterface::class);

        $this->assertTrue($reflection->isInterface());
        $this->assertFalse($reflection->isInstantiable());
    }

    protected function makeExporter(): ExporterInterface
    {
        return new class implements ExporterInterface
        {
            public function export()
            {
                return 'exported_data';
            }
        };
    }
}
