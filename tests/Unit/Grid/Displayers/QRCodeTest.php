<?php

namespace Dcat\Admin\Tests\Unit\Grid\Displayers;

use Dcat\Admin\Grid;
use Dcat\Admin\Grid\Column;
use Dcat\Admin\Grid\Displayers\QRCode;
use Dcat\Admin\Tests\TestCase;
use Mockery;

class QRCodeTest extends TestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }

    protected function makeDisplayer($value): QRCode
    {
        $grid = Mockery::mock(Grid::class);
        $grid->shouldReceive('getName')->andReturn('test');

        $column = Mockery::mock(Column::class);
        $column->shouldReceive('getName')->andReturn('url');
        $column->shouldReceive('getOriginal')->andReturn($value);

        $row = ['id' => 1, 'url' => $value];

        return new QRCode($value, $grid, $column, $row);
    }

    public function test_display_contains_qrcode_class(): void
    {
        $displayer = $this->makeDisplayer('https://example.com');
        $result = $displayer->display();

        $this->assertStringContainsString('grid-column-qrcode', $result);
    }

    public function test_display_contains_data_text_attribute(): void
    {
        $displayer = $this->makeDisplayer('https://example.com');
        $result = $displayer->display();

        $this->assertStringContainsString('data-text="https://example.com"', $result);
    }

    public function test_display_contains_default_dimensions(): void
    {
        $displayer = $this->makeDisplayer('https://example.com');
        $result = $displayer->display();

        $this->assertStringContainsString('data-width="150"', $result);
        $this->assertStringContainsString('data-height="150"', $result);
    }

    public function test_display_with_custom_dimensions(): void
    {
        $displayer = $this->makeDisplayer('https://example.com');
        $result = $displayer->display(null, 200, 200);

        $this->assertStringContainsString('data-width="200"', $result);
        $this->assertStringContainsString('data-height="200"', $result);
    }

    public function test_display_contains_value(): void
    {
        $displayer = $this->makeDisplayer('https://example.com');
        $result = $displayer->display();

        $this->assertStringContainsString('https://example.com', $result);
    }

    public function test_display_contains_qrcode_icon(): void
    {
        $displayer = $this->makeDisplayer('test');
        $result = $displayer->display();

        $this->assertStringContainsString('fa-qrcode', $result);
    }

    public function test_display_with_formatter_closure(): void
    {
        $displayer = $this->makeDisplayer('original');
        $formatter = function ($content) {
            return 'formatted:'.$content;
        };
        $result = $displayer->display($formatter);

        $this->assertStringContainsString('data-text="formatted:original"', $result);
    }
}
