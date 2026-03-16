<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Grid\Exporters;

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

    protected function makeExporterWithoutConstructor(): ExcelExporter
    {
        $reflection = new \ReflectionClass(ExcelExporter::class);

        return $reflection->newInstanceWithoutConstructor();
    }

    protected function getProtectedProperty(object $object, string $property)
    {
        $reflection = new \ReflectionProperty($object, $property);
        $reflection->setAccessible(true);

        return $reflection->getValue($object);
    }

    public function test_export_method_is_public_and_declared_in_excel_exporter(): void
    {
        $reflection = new \ReflectionMethod(ExcelExporter::class, 'export');

        $this->assertTrue($reflection->isPublic());
        $this->assertSame(ExcelExporter::class, $reflection->getDeclaringClass()->getName());
        $this->assertCount(0, $reflection->getParameters());
    }

    public function test_constructor_accepts_titles_parameter_with_empty_array_default(): void
    {
        $reflection = new \ReflectionMethod(ExcelExporter::class, '__construct');
        $parameters = $reflection->getParameters();

        $this->assertCount(1, $parameters);
        $this->assertSame('titles', $parameters[0]->getName());
        $this->assertTrue($parameters[0]->isDefaultValueAvailable());
        $this->assertSame([], $parameters[0]->getDefaultValue());
    }

    public function test_titles_setter_and_getter_work_on_exporter(): void
    {
        $exporter = $this->makeExporterWithoutConstructor();

        $result = $exporter->titles(['id' => 'ID', 'name' => 'Name']);

        $this->assertSame($exporter, $result);
        $this->assertSame(['id' => 'ID', 'name' => 'Name'], $exporter->titles());
    }

    public function test_filename_setter_affects_get_filename(): void
    {
        $exporter = $this->makeExporterWithoutConstructor();

        $result = $exporter->filename('users-export');

        $this->assertSame($exporter, $result);
        $this->assertSame('users-export', $exporter->getFilename());
    }

    public function test_with_scope_sets_scope_property(): void
    {
        $exporter = $this->makeExporterWithoutConstructor();

        $result = $exporter->withScope('all');

        $this->assertSame($exporter, $result);
        $this->assertSame('all', $this->getProtectedProperty($exporter, 'scope'));
    }

    public function test_extension_helpers_update_extension_property(): void
    {
        $exporter = $this->makeExporterWithoutConstructor();

        $exporter->csv();
        $this->assertSame('csv', $this->getProtectedProperty($exporter, 'extension'));

        $exporter->ods();
        $this->assertSame('ods', $this->getProtectedProperty($exporter, 'extension'));

        $exporter->xlsx();
        $this->assertSame('xlsx', $this->getProtectedProperty($exporter, 'extension'));
    }
}
