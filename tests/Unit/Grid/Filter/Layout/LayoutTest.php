<?php

namespace Dcat\Admin\Tests\Unit\Grid\Filter\Layout;

use Dcat\Admin\Grid\Filter;
use Dcat\Admin\Grid\Filter\AbstractFilter;
use Dcat\Admin\Grid\Filter\Layout\Column;
use Dcat\Admin\Grid\Filter\Layout\Layout;
use Dcat\Admin\Tests\TestCase;
use Illuminate\Support\Collection;
use Mockery;
use ReflectionProperty;

class LayoutTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    protected function makeLayout(): array
    {
        $filter = Mockery::mock(Filter::class);

        $layout = new Layout($filter);

        return [$layout, $filter];
    }

    protected function getProtectedProperty(object $object, string $property)
    {
        $ref = new ReflectionProperty($object, $property);
        $ref->setAccessible(true);

        return $ref->getValue($object);
    }

    public function test_constructor_creates_current_column(): void
    {
        [$layout] = $this->makeLayout();

        $current = $this->getProtectedProperty($layout, 'current');

        $this->assertInstanceOf(Column::class, $current);
    }

    public function test_constructor_creates_empty_columns(): void
    {
        [$layout] = $this->makeLayout();

        $columns = $this->getProtectedProperty($layout, 'columns');

        $this->assertInstanceOf(Collection::class, $columns);
        $this->assertTrue($columns->isEmpty());
    }

    public function test_add_filter_delegates_to_current(): void
    {
        [$layout] = $this->makeLayout();

        $mockFilter = Mockery::mock(AbstractFilter::class);
        $layout->addFilter($mockFilter);

        $current = $this->getProtectedProperty($layout, 'current');
        $this->assertCount(1, $current->filters());
    }

    public function test_columns_returns_collection(): void
    {
        [$layout] = $this->makeLayout();

        $columns = $layout->columns();

        $this->assertInstanceOf(Collection::class, $columns);
    }

    public function test_columns_includes_current_when_empty(): void
    {
        [$layout] = $this->makeLayout();

        $columns = $layout->columns();

        $this->assertCount(1, $columns);
        $current = $this->getProtectedProperty($layout, 'current');
        $this->assertSame($current, $columns->first());
    }

    public function test_column_first_call_uses_current(): void
    {
        [$layout, $filter] = $this->makeLayout();

        $currentBefore = $this->getProtectedProperty($layout, 'current');

        $layout->column(6, function ($f) {
            // closure receives parent filter
        });

        $currentAfter = $this->getProtectedProperty($layout, 'current');
        $this->assertSame($currentBefore, $currentAfter);
        $this->assertSame(6, $currentAfter->width());
    }

    public function test_column_creates_new_column(): void
    {
        [$layout, $filter] = $this->makeLayout();

        $layout->column(6, function ($f) {});
        $layout->column(6, function ($f) {});

        $columns = $layout->columns();

        $this->assertCount(2, $columns);
    }

    public function test_column_calls_closure_with_parent(): void
    {
        [$layout, $filter] = $this->makeLayout();

        $receivedFilter = null;
        $layout->column(12, function ($f) use (&$receivedFilter) {
            $receivedFilter = $f;
        });

        $this->assertSame($filter, $receivedFilter);
    }
}
