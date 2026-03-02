<?php

namespace Dcat\Admin\Tests\Unit\Grid\Exporters;

use Dcat\Admin\Grid\Exporters\AbstractExporter;
use Dcat\Admin\Grid\Exporters\ExcelExporter;
use Dcat\Admin\Tests\TestCase;
use Mockery;

class ExcelExporterTest extends TestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }

    public function test_class_exists(): void
    {
        $this->assertTrue(class_exists(ExcelExporter::class));
    }

    public function test_is_subclass_of_abstract_exporter(): void
    {
        $this->assertTrue(is_subclass_of(ExcelExporter::class, AbstractExporter::class));
    }

    public function test_method_export_exists(): void
    {
        $this->assertTrue(method_exists(ExcelExporter::class, 'export'));
    }

    public function test_export_is_public(): void
    {
        $ref = new \ReflectionMethod(ExcelExporter::class, 'export');

        $this->assertTrue($ref->isPublic());
    }

    public function test_export_overrides_parent(): void
    {
        $ref = new \ReflectionMethod(ExcelExporter::class, 'export');

        $this->assertEquals(ExcelExporter::class, $ref->getDeclaringClass()->getName());
    }

    public function test_export_has_no_parameters(): void
    {
        $ref = new \ReflectionMethod(ExcelExporter::class, 'export');

        $this->assertCount(0, $ref->getParameters());
    }

    public function test_constructor_exists(): void
    {
        $this->assertTrue(method_exists(ExcelExporter::class, '__construct'));
    }

    public function test_constructor_accepts_titles_parameter(): void
    {
        $ref = new \ReflectionMethod(ExcelExporter::class, '__construct');
        $params = $ref->getParameters();

        $this->assertCount(1, $params);
        $this->assertEquals('titles', $params[0]->getName());
    }

    public function test_constructor_titles_default_is_empty_array(): void
    {
        $ref = new \ReflectionMethod(ExcelExporter::class, '__construct');
        $params = $ref->getParameters();

        $this->assertTrue($params[0]->isDefaultValueAvailable());
        $this->assertSame([], $params[0]->getDefaultValue());
    }

    public function test_inherits_titles_method(): void
    {
        $this->assertTrue(method_exists(ExcelExporter::class, 'titles'));
    }

    public function test_inherits_filename_method(): void
    {
        $this->assertTrue(method_exists(ExcelExporter::class, 'filename'));
    }

    public function test_inherits_with_scope_method(): void
    {
        $this->assertTrue(method_exists(ExcelExporter::class, 'withScope'));
    }
}
