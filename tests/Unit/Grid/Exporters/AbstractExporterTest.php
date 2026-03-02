<?php

namespace Dcat\Admin\Tests\Unit\Grid\Exporters;

use Dcat\Admin\Grid\Exporters\AbstractExporter;
use Dcat\Admin\Grid\Exporters\ExporterInterface;
use Dcat\Admin\Tests\TestCase;
use Mockery;

class AbstractExporterTest extends TestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }

    protected function makeExporter(array $titles = []): AbstractExporter
    {
        return new class($titles) extends AbstractExporter
        {
            public function export() {}
        };
    }

    public function test_class_exists(): void
    {
        $this->assertTrue(class_exists(AbstractExporter::class));
    }

    public function test_is_abstract(): void
    {
        $ref = new \ReflectionClass(AbstractExporter::class);

        $this->assertTrue($ref->isAbstract());
    }

    public function test_implements_exporter_interface(): void
    {
        $ref = new \ReflectionClass(AbstractExporter::class);

        $this->assertTrue($ref->implementsInterface(ExporterInterface::class));
    }

    public function test_titles_default_is_empty_array(): void
    {
        $ref = new \ReflectionProperty(AbstractExporter::class, 'titles');
        $ref->setAccessible(true);

        $this->assertSame([], $ref->getDefaultValue());
    }

    public function test_extension_default_is_xlsx(): void
    {
        $ref = new \ReflectionProperty(AbstractExporter::class, 'extension');
        $ref->setAccessible(true);

        $this->assertSame('xlsx', $ref->getDefaultValue());
    }

    public function test_method_titles_exists(): void
    {
        $this->assertTrue(method_exists(AbstractExporter::class, 'titles'));
    }

    public function test_method_filename_exists(): void
    {
        $this->assertTrue(method_exists(AbstractExporter::class, 'filename'));
    }

    public function test_method_rows_exists(): void
    {
        $this->assertTrue(method_exists(AbstractExporter::class, 'rows'));
    }

    public function test_method_xlsx_exists(): void
    {
        $this->assertTrue(method_exists(AbstractExporter::class, 'xlsx'));
    }

    public function test_method_csv_exists(): void
    {
        $this->assertTrue(method_exists(AbstractExporter::class, 'csv'));
    }

    public function test_method_ods_exists(): void
    {
        $this->assertTrue(method_exists(AbstractExporter::class, 'ods'));
    }

    public function test_method_extension_exists(): void
    {
        $this->assertTrue(method_exists(AbstractExporter::class, 'extension'));
    }

    public function test_method_set_grid_exists(): void
    {
        $this->assertTrue(method_exists(AbstractExporter::class, 'setGrid'));
    }

    public function test_method_get_filename_exists(): void
    {
        $this->assertTrue(method_exists(AbstractExporter::class, 'getFilename'));
    }

    public function test_method_build_data_exists(): void
    {
        $this->assertTrue(method_exists(AbstractExporter::class, 'buildData'));
    }

    public function test_method_with_scope_exists(): void
    {
        $this->assertTrue(method_exists(AbstractExporter::class, 'withScope'));
    }

    public function test_method_make_exists(): void
    {
        $this->assertTrue(method_exists(AbstractExporter::class, 'make'));
    }

    public function test_constructor_with_titles_sets_titles(): void
    {
        $titles = ['id' => 'ID', 'name' => 'Name'];
        $exporter = $this->makeExporter($titles);

        $result = $exporter->titles();

        $this->assertSame($titles, $result);
    }

    public function test_titles_getter_returns_array(): void
    {
        $exporter = $this->makeExporter();

        $ref = new \ReflectionProperty($exporter, 'titles');
        $ref->setAccessible(true);
        $ref->setValue($exporter, ['col1' => 'Column 1']);

        $result = $exporter->titles();

        $this->assertIsArray($result);
        $this->assertSame(['col1' => 'Column 1'], $result);
    }

    public function test_filename_setter_returns_this(): void
    {
        $exporter = $this->makeExporter();

        $result = $exporter->filename('my_export');

        $this->assertSame($exporter, $result);
    }

    public function test_filename_setter_sets_filename(): void
    {
        $exporter = $this->makeExporter();
        $exporter->filename('my_export');

        $ref = new \ReflectionProperty($exporter, 'filename');
        $ref->setAccessible(true);

        $this->assertSame('my_export', $ref->getValue($exporter));
    }

    public function test_extension_setter_changes_extension(): void
    {
        $exporter = $this->makeExporter();
        $exporter->extension('csv');

        $ref = new \ReflectionProperty($exporter, 'extension');
        $ref->setAccessible(true);

        $this->assertSame('csv', $ref->getValue($exporter));
    }

    public function test_extension_setter_returns_this(): void
    {
        $exporter = $this->makeExporter();

        $result = $exporter->extension('ods');

        $this->assertSame($exporter, $result);
    }

    public function test_xlsx_sets_extension_to_xlsx(): void
    {
        $exporter = $this->makeExporter();
        $exporter->extension('csv'); // change first
        $exporter->xlsx();

        $ref = new \ReflectionProperty($exporter, 'extension');
        $ref->setAccessible(true);

        $this->assertSame('xlsx', $ref->getValue($exporter));
    }

    public function test_csv_sets_extension_to_csv(): void
    {
        $exporter = $this->makeExporter();
        $exporter->csv();

        $ref = new \ReflectionProperty($exporter, 'extension');
        $ref->setAccessible(true);

        $this->assertSame('csv', $ref->getValue($exporter));
    }

    public function test_ods_sets_extension_to_ods(): void
    {
        $exporter = $this->makeExporter();
        $exporter->ods();

        $ref = new \ReflectionProperty($exporter, 'extension');
        $ref->setAccessible(true);

        $this->assertSame('ods', $ref->getValue($exporter));
    }

    public function test_make_returns_instance(): void
    {
        $class = get_class($this->makeExporter());

        $instance = $class::make();

        $this->assertInstanceOf(AbstractExporter::class, $instance);
    }

    public function test_rows_sets_builder_and_returns_this(): void
    {
        $exporter = $this->makeExporter();
        $closure = function ($data) {
            return $data;
        };

        $result = $exporter->rows($closure);

        $this->assertSame($exporter, $result);

        $ref = new \ReflectionProperty($exporter, 'builder');
        $ref->setAccessible(true);

        $this->assertSame($closure, $ref->getValue($exporter));
    }

    public function test_titles_setter_with_array_returns_this(): void
    {
        $exporter = $this->makeExporter();

        $result = $exporter->titles(['id' => 'ID']);

        $this->assertSame($exporter, $result);
    }

    public function test_titles_setter_with_false_disables_titles(): void
    {
        $exporter = $this->makeExporter();
        $exporter->titles(false);

        $ref = new \ReflectionProperty($exporter, 'titles');
        $ref->setAccessible(true);

        $this->assertFalse($ref->getValue($exporter));
    }

    public function test_get_filename_returns_custom_filename(): void
    {
        $exporter = $this->makeExporter();
        $exporter->filename('custom_file');

        $this->assertSame('custom_file', $exporter->getFilename());
    }

    public function test_default_constructor_has_empty_titles(): void
    {
        $exporter = $this->makeExporter();

        $ref = new \ReflectionProperty($exporter, 'titles');
        $ref->setAccessible(true);

        $this->assertSame([], $ref->getValue($exporter));
    }
}
