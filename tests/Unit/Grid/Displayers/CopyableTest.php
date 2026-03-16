<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Grid\Displayers;

use Dcat\Admin\Grid;
use Dcat\Admin\Grid\Column;
use Dcat\Admin\Grid\Displayers\Copyable;
use Dcat\Admin\Tests\TestCase;
use Mockery;

class CopyableTest extends TestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }

    protected function makeDisplayer($value): Copyable
    {
        $grid = Mockery::mock(Grid::class);
        $grid->shouldReceive('getName')->andReturn('test');

        $column = Mockery::mock(Column::class);
        $column->shouldReceive('getName')->andReturn('code');

        $row = ['id' => 1, 'code' => $value];

        return new Copyable($value, $grid, $column, $row);
    }

    public function test_display_contains_copy_icon(): void
    {
        $displayer = $this->makeDisplayer('ABC123');
        $result = $displayer->display();

        $this->assertStringContainsString('fa-copy', $result);
    }

    public function test_display_contains_copyable_class(): void
    {
        $displayer = $this->makeDisplayer('test-value');
        $result = $displayer->display();

        $this->assertStringContainsString('grid-column-copyable', $result);
    }

    public function test_display_contains_data_content(): void
    {
        $displayer = $this->makeDisplayer('copy-this');
        $result = $displayer->display();

        $this->assertStringContainsString('data-content="copy-this"', $result);
    }

    public function test_display_shows_value_text(): void
    {
        $displayer = $this->makeDisplayer('visible-text');
        $result = $displayer->display();

        // Value should be displayed after the copy icon
        $this->assertStringContainsString('visible-text', $result);
    }

    public function test_display_empty_string_returns_empty(): void
    {
        $displayer = $this->makeDisplayer('');
        $result = $displayer->display();

        $this->assertSame('', $result);
    }

    public function test_display_null_returns_empty(): void
    {
        $displayer = $this->makeDisplayer(null);
        $result = $displayer->display();

        // null is converted to '' by htmlEntityEncode, then returns ''
        $this->assertSame('', $result);
    }

    public function test_display_html_entities_encoded(): void
    {
        $displayer = $this->makeDisplayer('<b>bold</b>');
        $result = $displayer->display();

        $this->assertStringNotContainsString('<b>bold</b>', $result);
        $this->assertStringContainsString('&lt;b&gt;bold&lt;/b&gt;', $result);
    }

    public function test_display_has_tooltip(): void
    {
        $displayer = $this->makeDisplayer('test');
        $result = $displayer->display();

        $this->assertStringContainsString('data-placement="bottom"', $result);
    }
}
