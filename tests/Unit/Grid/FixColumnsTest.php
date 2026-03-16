<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Grid;

use Dcat\Admin\Grid;
use Dcat\Admin\Grid\FixColumns;
use Dcat\Admin\Tests\TestCase;
use Illuminate\Support\Collection;
use Mockery;

class FixColumnsTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    protected function createMockGrid(): Grid
    {
        return Mockery::mock(Grid::class);
    }

    public function test_constructor_sets_head_and_tail_values(): void
    {
        $grid = $this->createMockGrid();
        $fix = new FixColumns($grid, 3, -2);

        $this->assertSame(3, $fix->head);
        $this->assertSame(-2, $fix->tail);
    }

    public function test_default_tail_is_negative_one(): void
    {
        $grid = $this->createMockGrid();
        $fix = new FixColumns($grid, 5);

        $this->assertSame(-1, $fix->tail);
    }

    public function test_left_columns_returns_empty_collection_initially(): void
    {
        $grid = $this->createMockGrid();
        $fix = new FixColumns($grid, 2);

        $left = $fix->leftColumns();

        $this->assertInstanceOf(Collection::class, $left);
        $this->assertTrue($left->isEmpty());
    }

    public function test_right_columns_returns_empty_collection_initially(): void
    {
        $grid = $this->createMockGrid();
        $fix = new FixColumns($grid, 2);

        $right = $fix->rightColumns();

        $this->assertInstanceOf(Collection::class, $right);
        $this->assertTrue($right->isEmpty());
    }

    public function test_left_complex_columns_returns_empty_collection(): void
    {
        $grid = $this->createMockGrid();
        $fix = new FixColumns($grid, 2);

        $complexLeft = $fix->leftComplexColumns();

        $this->assertInstanceOf(Collection::class, $complexLeft);
        $this->assertTrue($complexLeft->isEmpty());
    }

    public function test_right_complex_columns_returns_empty_collection(): void
    {
        $grid = $this->createMockGrid();
        $fix = new FixColumns($grid, 2);

        $complexRight = $fix->rightComplexColumns();

        $this->assertInstanceOf(Collection::class, $complexRight);
        $this->assertTrue($complexRight->isEmpty());
    }

    public function test_height_sets_value_and_returns_self(): void
    {
        $grid = $this->createMockGrid();
        $fix = new FixColumns($grid, 2);

        $result = $fix->height(500);

        $this->assertSame($fix, $result);

        // Verify the height was stored via reflection
        $ref = new \ReflectionProperty(FixColumns::class, 'height');
        $ref->setAccessible(true);
        $this->assertSame(500, $ref->getValue($fix));
    }

    public function test_head_and_tail_are_public_properties(): void
    {
        $ref = new \ReflectionClass(FixColumns::class);

        $headProp = $ref->getProperty('head');
        $tailProp = $ref->getProperty('tail');

        $this->assertTrue($headProp->isPublic());
        $this->assertTrue($tailProp->isPublic());
    }
}
