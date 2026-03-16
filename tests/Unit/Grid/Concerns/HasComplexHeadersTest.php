<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Grid\Concerns;

use Dcat\Admin\Exception\InvalidArgumentException;
use Dcat\Admin\Grid;
use Dcat\Admin\Grid\ComplexHeader;
use Dcat\Admin\Tests\TestCase;
use Illuminate\Support\Collection;
use Mockery;

class HasComplexHeadersTest extends TestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }

    protected function createGridMock(): Grid
    {
        $grid = Mockery::mock(Grid::class)->makePartial();
        $grid->shouldReceive('withBorder')->andReturnSelf();

        return $grid;
    }

    public function test_complex_headers_initially_null(): void
    {
        $grid = $this->createGridMock();

        $this->assertNull($grid->getComplexHeaders());
    }

    public function test_combine_creates_complex_header(): void
    {
        $grid = $this->createGridMock();

        $result = $grid->combine('combined', ['col1', 'col2'], 'Combined');

        $this->assertInstanceOf(ComplexHeader::class, $result);
    }

    public function test_combine_throws_with_less_than_two_columns(): void
    {
        $grid = $this->createGridMock();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid column names.');

        $grid->combine('combined', ['col1'], 'Combined');
    }

    public function test_combine_returns_complex_header(): void
    {
        $grid = $this->createGridMock();

        $result = $grid->combine('header', ['col_a', 'col_b'], 'Header');

        $this->assertInstanceOf(ComplexHeader::class, $result);
    }

    public function test_get_complex_headers_returns_null_initially(): void
    {
        $grid = $this->createGridMock();

        $this->assertNull($grid->getComplexHeaders());
    }

    public function test_get_complex_headers_returns_collection_after_combine(): void
    {
        $grid = $this->createGridMock();

        $grid->combine('group', ['col1', 'col2'], 'Group');

        $headers = $grid->getComplexHeaders();

        $this->assertInstanceOf(Collection::class, $headers);
        $this->assertCount(1, $headers);
    }

    public function test_get_complex_header_names_returns_empty_initially(): void
    {
        $grid = $this->createGridMock();

        $this->assertSame([], $grid->getComplexHeaderNames());
    }

    public function test_combine_calls_with_border(): void
    {
        $grid = Mockery::mock(Grid::class)->makePartial();
        $grid->shouldReceive('withBorder')->once()->andReturnSelf();

        $grid->combine('group', ['col1', 'col2'], 'Group');
    }
}
