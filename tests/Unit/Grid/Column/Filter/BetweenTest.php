<?php

namespace Dcat\Admin\Tests\Unit\Grid\Column\Filter;

use Dcat\Admin\Grid\Column\Filter;
use Dcat\Admin\Grid\Column\Filter\Between;
use Dcat\Admin\Grid\Model;
use Dcat\Admin\Tests\TestCase;
use Mockery;

class TestableBetween extends Between
{
    public $lastQuery = null;

    public $lastParams = null;

    public $lastMethod = null;

    protected function withQuery($model, string $query, array $params)
    {
        $this->lastMethod = $query;
        $this->lastParams = $params;
    }

    protected function requireAssets()
    {
        // no-op in tests
    }
}

class BetweenTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_extends_filter(): void
    {
        $filter = new Between;
        $this->assertInstanceOf(Filter::class, $filter);
    }

    public function test_constructor_creates_class_array_with_start_and_end(): void
    {
        $filter = new Between;

        $ref = new \ReflectionProperty(Filter::class, 'class');
        $ref->setAccessible(true);
        $class = $ref->getValue($filter);

        $this->assertIsArray($class);
        $this->assertStringStartsWith('column-filter-start-', $class['start'] ?? '');
        $this->assertStringStartsWith('column-filter-end-', $class['end'] ?? '');
    }

    public function test_to_timestamp_sets_flag(): void
    {
        $filter = new Between;
        $result = $filter->toTimestamp();

        $this->assertSame($filter, $result);

        $ref = new \ReflectionProperty(Between::class, 'timestamp');
        $ref->setAccessible(true);
        $this->assertTrue($ref->getValue($filter));
    }

    public function test_date_sets_format(): void
    {
        $filter = new TestableBetween;
        $result = $filter->date();

        $this->assertSame($filter, $result);

        $ref = new \ReflectionProperty(Between::class, 'dateFormat');
        $ref->setAccessible(true);
        $this->assertEquals('YYYY-MM-DD', $ref->getValue($filter));
    }

    public function test_time_sets_format(): void
    {
        $filter = new TestableBetween;
        $result = $filter->time();

        $this->assertSame($filter, $result);

        $ref = new \ReflectionProperty(Between::class, 'dateFormat');
        $ref->setAccessible(true);
        $this->assertEquals('HH:mm:ss', $ref->getValue($filter));
    }

    public function test_datetime_sets_format(): void
    {
        $filter = new TestableBetween;
        $result = $filter->datetime();

        $this->assertSame($filter, $result);

        $ref = new \ReflectionProperty(Between::class, 'dateFormat');
        $ref->setAccessible(true);
        $this->assertEquals('YYYY-MM-DD HH:mm:ss', $ref->getValue($filter));
    }

    public function test_add_binding_skips_empty_array(): void
    {
        $filter = new TestableBetween;
        $model = Mockery::mock(Model::class);

        $filter->addBinding([], $model);

        $this->assertNull($filter->lastMethod);
    }

    public function test_add_binding_end_only_uses_lte(): void
    {
        $filter = new TestableBetween;
        $filter->setColumnName('created_at');
        $model = Mockery::mock(Model::class);

        $filter->addBinding(['end' => '2024-12-31'], $model);

        $this->assertEquals('where', $filter->lastMethod);
        $this->assertEquals(['<=', '2024-12-31'], $filter->lastParams);
    }

    public function test_add_binding_start_only_uses_gte(): void
    {
        $filter = new TestableBetween;
        $filter->setColumnName('created_at');
        $model = Mockery::mock(Model::class);

        $filter->addBinding(['start' => '2024-01-01'], $model);

        $this->assertEquals('where', $filter->lastMethod);
        $this->assertEquals(['>=', '2024-01-01'], $filter->lastParams);
    }

    public function test_add_binding_both_uses_where_between(): void
    {
        $filter = new TestableBetween;
        $filter->setColumnName('created_at');
        $model = Mockery::mock(Model::class);

        $filter->addBinding(['start' => '2024-01-01', 'end' => '2024-12-31'], $model);

        $this->assertEquals('whereBetween', $filter->lastMethod);
        $this->assertEquals([['2024-01-01', '2024-12-31']], $filter->lastParams);
    }

    public function test_add_binding_with_timestamp_conversion(): void
    {
        $filter = new TestableBetween;
        $filter->setColumnName('created_at');
        $filter->toTimestamp();
        $model = Mockery::mock(Model::class);

        $filter->addBinding(['start' => '2024-01-01', 'end' => '2024-12-31'], $model);

        $this->assertEquals('whereBetween', $filter->lastMethod);
        $expectedStart = strtotime('2024-01-01');
        $expectedEnd = strtotime('2024-12-31');
        $this->assertEquals([[$expectedStart, $expectedEnd]], $filter->lastParams);
    }
}
