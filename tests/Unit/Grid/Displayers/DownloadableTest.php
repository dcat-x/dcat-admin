<?php

namespace Dcat\Admin\Tests\Unit\Grid\Displayers;

use Dcat\Admin\Grid;
use Dcat\Admin\Grid\Column;
use Dcat\Admin\Grid\Displayers\Downloadable;
use Dcat\Admin\Tests\TestCase;
use Illuminate\Support\Facades\Storage;
use Mockery;

class DownloadableTest extends TestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }

    protected function makeDisplayer($value): Downloadable
    {
        $grid = Mockery::mock(Grid::class);
        $grid->shouldReceive('getName')->andReturn('test');

        $column = Mockery::mock(Column::class);
        $column->shouldReceive('getName')->andReturn('file');

        $row = ['id' => 1, 'file' => $value];

        return new Downloadable($value, $grid, $column, $row);
    }

    public function test_display_with_full_url(): void
    {
        $displayer = $this->makeDisplayer('https://example.com/files/report.pdf');
        $result = $displayer->display();

        $this->assertStringContainsString('href=\'https://example.com/files/report.pdf\'', $result);
        $this->assertStringContainsString('download=\'report.pdf\'', $result);
        $this->assertStringContainsString('report.pdf', $result);
    }

    public function test_display_with_server_prefix(): void
    {
        $displayer = $this->makeDisplayer('uploads/document.docx');
        $result = $displayer->display('https://cdn.example.com');

        $this->assertStringContainsString('href=\'https://cdn.example.com/uploads/document.docx\'', $result);
        $this->assertStringContainsString('download=\'document.docx\'', $result);
    }

    public function test_display_with_server_trailing_slash(): void
    {
        $displayer = $this->makeDisplayer('/uploads/file.zip');
        $result = $displayer->display('https://cdn.example.com/');

        $this->assertStringContainsString('href=\'https://cdn.example.com/uploads/file.zip\'', $result);
    }

    public function test_display_with_disk(): void
    {
        Storage::fake('public');
        $displayer = $this->makeDisplayer('uploads/test.pdf');
        $result = $displayer->display('', 'public');

        $this->assertStringContainsString('download=\'test.pdf\'', $result);
        $this->assertStringContainsString('icon-download', $result);
    }

    public function test_display_contains_download_icon(): void
    {
        $displayer = $this->makeDisplayer('https://example.com/file.txt');
        $result = $displayer->display();

        $this->assertStringContainsString('icon-download', $result);
    }

    public function test_display_contains_anchor_tag(): void
    {
        $displayer = $this->makeDisplayer('https://example.com/file.txt');
        $result = $displayer->display();

        $this->assertStringContainsString('<a href=', $result);
        $this->assertStringContainsString("target='_blank'", $result);
    }

    public function test_display_with_array_of_files(): void
    {
        $files = ['https://example.com/a.pdf', 'https://example.com/b.pdf'];
        $displayer = $this->makeDisplayer($files);
        $result = $displayer->display();

        $this->assertStringContainsString('a.pdf', $result);
        $this->assertStringContainsString('b.pdf', $result);
        $this->assertStringContainsString('<br>', $result);
    }

    public function test_display_with_empty_value_returns_empty(): void
    {
        $displayer = $this->makeDisplayer('');
        $result = $displayer->display();

        $this->assertEmpty($result);
    }

    public function test_display_with_null_value_returns_empty(): void
    {
        $displayer = $this->makeDisplayer(null);
        $result = $displayer->display();

        $this->assertEmpty($result);
    }

    public function test_display_extracts_basename_from_path(): void
    {
        $displayer = $this->makeDisplayer('https://example.com/path/to/deep/file.csv');
        $result = $displayer->display();

        $this->assertStringContainsString('download=\'file.csv\'', $result);
        $this->assertStringContainsString('file.csv', $result);
    }

    public function test_display_with_text_muted_class(): void
    {
        $displayer = $this->makeDisplayer('https://example.com/file.txt');
        $result = $displayer->display();

        $this->assertStringContainsString('text-muted', $result);
    }
}
