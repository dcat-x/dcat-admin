<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Grid\Importers;

use Dcat\Admin\Grid\Importers\ExcelImporter;
use Dcat\Admin\Tests\TestCase;

class ExcelImporterTest extends TestCase
{
    public function test_titles_returns_manual_titles_without_grid(): void
    {
        $importer = ExcelImporter::make();
        $importer->titles(['name' => 'Name', 'email' => 'Email']);

        $this->assertSame(['name' => 'Name', 'email' => 'Email'], $importer->titles());
    }

    public function test_titles_returns_empty_array_without_grid_and_no_manual_titles(): void
    {
        $importer = ExcelImporter::make();

        $this->assertSame([], $importer->titles());
    }
}
