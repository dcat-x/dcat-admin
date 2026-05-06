<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Grid\Column;

use Dcat\Admin\Grid\Column\Filter;
use Dcat\Admin\Grid\Column\ValueFilter;
use Dcat\Admin\Tests\TestCase;
use Mockery;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;

#[AllowMockObjectsWithoutExpectations]
class ValueFilterTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    private function createMockFilter(): Filter
    {
        $filter = Mockery::mock(Filter::class)->makePartial();
        $filter->shouldReceive('getQueryName')->andReturn('filter-test')->byDefault();
        $filter->shouldReceive('value')->andReturn('test_value')->byDefault();

        return $filter;
    }

    public function test_constructor_stores_filter(): void
    {
        $filter = $this->createMockFilter();
        $valueFilter = new ValueFilter($filter, 'key');

        $ref = new \ReflectionProperty(ValueFilter::class, 'filter');
        $ref->setAccessible(true);
        $this->assertSame($filter, $ref->getValue($valueFilter));
    }

    public function test_constructor_stores_value_key(): void
    {
        $filter = $this->createMockFilter();
        $valueFilter = new ValueFilter($filter, 'my_key');

        $ref = new \ReflectionProperty(ValueFilter::class, 'valueKey');
        $ref->setAccessible(true);
        $this->assertSame('my_key', $ref->getValue($valueFilter));
    }

    public function test_constructor_stores_null_value_key(): void
    {
        $filter = $this->createMockFilter();
        $valueFilter = new ValueFilter($filter, null);

        $ref = new \ReflectionProperty(ValueFilter::class, 'valueKey');
        $ref->setAccessible(true);
        $this->assertNull($ref->getValue($valueFilter));
    }

    public function test_constructor_stores_closure_value_key(): void
    {
        $filter = $this->createMockFilter();
        $closure = function () {
            return 'value';
        };
        $valueFilter = new ValueFilter($filter, $closure);

        $ref = new \ReflectionProperty(ValueFilter::class, 'valueKey');
        $ref->setAccessible(true);
        $this->assertSame($closure, $ref->getValue($valueFilter));
    }

    public function test_get_query_name_delegates_to_filter(): void
    {
        $filter = $this->createMockFilter();
        $filter->shouldReceive('getQueryName')->andReturn('filter-column_name');

        $valueFilter = new ValueFilter($filter, 'key');

        $this->assertSame('filter-column_name', $valueFilter->getQueryName());
    }

    public function test_value_delegates_to_filter(): void
    {
        $filter = $this->createMockFilter();
        $filter->shouldReceive('value')->andReturn('some_value');

        $valueFilter = new ValueFilter($filter, 'key');

        $this->assertSame('some_value', $valueFilter->value());
    }

    public function test_value_returns_empty_string_default(): void
    {
        $filter = $this->createMockFilter();
        $filter->shouldReceive('value')->andReturn('');

        $valueFilter = new ValueFilter($filter, null);

        $this->assertSame('', $valueFilter->value());
    }

    public function test_get_query_name_returns_string(): void
    {
        $filter = $this->createMockFilter();
        $filter->shouldReceive('getQueryName')->andReturn('grid_filter-status');

        $valueFilter = new ValueFilter($filter, 'status');

        $this->assertIsString($valueFilter->getQueryName());
    }
}
