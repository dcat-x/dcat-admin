<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Grid\Displayers;

use Dcat\Admin\Grid;
use Dcat\Admin\Grid\Column;
use Dcat\Admin\Grid\Displayers\Link;
use Dcat\Admin\Tests\TestCase;
use Mockery;

class LinkTest extends TestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }

    protected function makeDisplayer($value): Link
    {
        $grid = Mockery::mock(Grid::class);
        $grid->shouldReceive('getName')->andReturn('test');

        $column = Mockery::mock(Column::class);
        $column->shouldReceive('getName')->andReturn('url');

        $row = ['id' => 1, 'url' => $value, 'name' => 'Test'];

        return new Link($value, $grid, $column, $row);
    }

    public function test_display_with_value_as_href(): void
    {
        $displayer = $this->makeDisplayer('https://example.com');
        $result = $displayer->display();

        $this->assertStringContainsString("href='https://example.com'", $result);
        $this->assertStringContainsString('https://example.com</a>', $result);
        $this->assertStringContainsString("target='_blank'", $result);
    }

    public function test_display_with_custom_href(): void
    {
        $displayer = $this->makeDisplayer('Click Me');
        $result = $displayer->display('https://custom.com');

        $this->assertStringContainsString("href='https://custom.com'", $result);
        $this->assertStringContainsString('Click Me</a>', $result);
    }

    public function test_display_with_custom_target(): void
    {
        $displayer = $this->makeDisplayer('https://example.com');
        $result = $displayer->display('', '_self');

        $this->assertStringContainsString("target='_self'", $result);
    }

    public function test_display_with_closure_href(): void
    {
        $displayer = $this->makeDisplayer('my-text');
        $result = $displayer->display(function ($value) {
            return 'https://example.com/'.$value;
        });

        $this->assertStringContainsString("href='https://example.com/my-text'", $result);
        $this->assertStringContainsString('my-text</a>', $result);
    }

    public function test_display_closure_has_access_to_row(): void
    {
        $displayer = $this->makeDisplayer('link-text');
        $result = $displayer->display(function ($value) {
            // $this is bound to row (a Fluent)
            return 'https://example.com/'.$this->id;
        });

        $this->assertStringContainsString("href='https://example.com/1'", $result);
    }

    public function test_display_returns_anchor_tag(): void
    {
        $displayer = $this->makeDisplayer('test');
        $result = $displayer->display();

        $this->assertStringStartsWith('<a ', $result);
        $this->assertStringEndsWith('</a>', $result);
    }

    public function test_display_with_empty_href_uses_value(): void
    {
        $displayer = $this->makeDisplayer('https://fallback.com');
        $result = $displayer->display('');

        $this->assertStringContainsString("href='https://fallback.com'", $result);
    }
}
