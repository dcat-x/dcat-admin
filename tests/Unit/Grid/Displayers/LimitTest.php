<?php

namespace Dcat\Admin\Tests\Unit\Grid\Displayers;

use Dcat\Admin\Grid;
use Dcat\Admin\Grid\Column;
use Dcat\Admin\Grid\Displayers\Limit;
use Dcat\Admin\Tests\TestCase;
use Mockery;

class LimitTest extends TestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }

    protected function makeDisplayer($value): Limit
    {
        $grid = Mockery::mock(Grid::class);
        $grid->shouldReceive('getName')->andReturn('test');

        $column = Mockery::mock(Column::class);
        $column->shouldReceive('getName')->andReturn('content');

        $row = ['id' => 1, 'content' => $value];

        return new Limit($value, $grid, $column, $row);
    }

    public function test_display_short_string_unchanged(): void
    {
        $displayer = $this->makeDisplayer('Hello');
        $result = $displayer->display(100);

        $this->assertSame('Hello', $result);
    }

    public function test_display_string_exceeding_limit(): void
    {
        $longString = str_repeat('a', 200);
        $displayer = $this->makeDisplayer($longString);
        $result = $displayer->display(50);

        $this->assertStringContainsString('limit-text', $result);
        $this->assertStringContainsString('limit-more', $result);
        $this->assertStringContainsString('...', $result);
    }

    public function test_display_string_with_custom_end(): void
    {
        $longString = str_repeat('x', 200);
        $displayer = $this->makeDisplayer($longString);
        $result = $displayer->display(50, '>>>');

        $this->assertStringContainsString('>>>', $result);
    }

    public function test_display_string_at_exact_limit(): void
    {
        $exactString = str_repeat('b', 100);
        $displayer = $this->makeDisplayer($exactString);
        $result = $displayer->display(100);

        // Exactly at limit, no truncation
        $this->assertSame(htmlentities($exactString), $result);
    }

    public function test_display_array_within_limit(): void
    {
        $displayer = $this->makeDisplayer(['a', 'b', 'c']);
        $result = $displayer->display(5);

        $this->assertIsArray($result);
        $this->assertCount(3, $result);
        $this->assertSame(['a', 'b', 'c'], $result);
    }

    public function test_display_array_exceeding_limit(): void
    {
        $displayer = $this->makeDisplayer(['a', 'b', 'c', 'd', 'e']);
        $result = $displayer->display(3);

        $this->assertIsArray($result);
        $this->assertCount(4, $result); // 3 items + '...'
        $this->assertSame(['a', 'b', 'c', '...'], $result);
    }

    public function test_display_array_with_custom_end(): void
    {
        $displayer = $this->makeDisplayer(['a', 'b', 'c', 'd']);
        $result = $displayer->display(2, '..more');

        $this->assertIsArray($result);
        $this->assertCount(3, $result);
        $this->assertSame('..more', $result[2]);
    }

    public function test_display_html_entities_encoded(): void
    {
        $displayer = $this->makeDisplayer('<script>alert("xss")</script>');
        $result = $displayer->display(100);

        // HTML entities should be encoded
        $this->assertStringNotContainsString('<script>', $result);
    }

    public function test_display_truncated_string_contains_expand_and_collapse(): void
    {
        $longString = str_repeat('z', 200);
        $displayer = $this->makeDisplayer($longString);
        $result = $displayer->display(50);

        // Should contain both collapsed and expanded versions
        $this->assertStringContainsString('fa-angle-double-down', $result);
        $this->assertStringContainsString('fa-angle-double-up', $result);
        $this->assertStringContainsString('d-none', $result);
    }
}
