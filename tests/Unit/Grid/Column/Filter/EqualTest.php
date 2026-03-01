<?php

namespace Dcat\Admin\Tests\Unit\Grid\Column\Filter;

use Dcat\Admin\Grid\Column\Filter;
use Dcat\Admin\Grid\Column\Filter\Equal;
use Dcat\Admin\Grid\Model;
use Dcat\Admin\Tests\TestCase;
use Mockery;

class TestableEqual extends Equal
{
    public array $queryLog = [];

    protected function withQuery($model, string $query, array $params): void
    {
        $this->queryLog[] = compact('query', 'params');
    }
}

class EqualTest extends TestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }

    public function test_constructor_sets_placeholder(): void
    {
        $filter = new TestableEqual('Search here');

        $ref = new \ReflectionProperty(Equal::class, 'placeholder');
        $ref->setAccessible(true);

        $this->assertSame('Search here', $ref->getValue($filter));
    }

    public function test_constructor_generates_unique_class(): void
    {
        $filter1 = new TestableEqual;
        $filter2 = new TestableEqual;

        $ref = new \ReflectionProperty(Filter::class, 'class');
        $ref->setAccessible(true);

        $class1 = $ref->getValue($filter1);
        $class2 = $ref->getValue($filter2);

        $this->assertStringStartsWith('column-filter-', $class1);
        $this->assertStringStartsWith('column-filter-', $class2);
        $this->assertNotSame($class1, $class2);
    }

    public function test_date_sets_date_format(): void
    {
        $filter = new TestableEqual;
        $result = $filter->date();

        $ref = new \ReflectionProperty(Equal::class, 'dateFormat');
        $ref->setAccessible(true);

        $this->assertSame('YYYY-MM-DD', $ref->getValue($filter));
        $this->assertSame($filter, $result);
    }

    public function test_time_sets_time_format(): void
    {
        $filter = new TestableEqual;
        $result = $filter->time();

        $ref = new \ReflectionProperty(Equal::class, 'dateFormat');
        $ref->setAccessible(true);

        $this->assertSame('HH:mm:ss', $ref->getValue($filter));
        $this->assertSame($filter, $result);
    }

    public function test_datetime_sets_datetime_format(): void
    {
        $filter = new TestableEqual;
        $result = $filter->datetime();

        $ref = new \ReflectionProperty(Equal::class, 'dateFormat');
        $ref->setAccessible(true);

        $this->assertSame('YYYY-MM-DD HH:mm:ss', $ref->getValue($filter));
        $this->assertSame($filter, $result);
    }

    public function test_datetime_accepts_custom_format(): void
    {
        $filter = new TestableEqual;
        $filter->datetime('DD/MM/YYYY');

        $ref = new \ReflectionProperty(Equal::class, 'dateFormat');
        $ref->setAccessible(true);

        $this->assertSame('DD/MM/YYYY', $ref->getValue($filter));
    }

    public function test_add_binding_with_valid_value(): void
    {
        $filter = new TestableEqual;
        $model = Mockery::mock(Model::class);

        $filter->addBinding('test_value', $model);

        $this->assertCount(1, $filter->queryLog);
        $this->assertSame('where', $filter->queryLog[0]['query']);
        $this->assertSame(['test_value'], $filter->queryLog[0]['params']);
    }

    public function test_add_binding_trims_whitespace(): void
    {
        $filter = new TestableEqual;
        $model = Mockery::mock(Model::class);

        $filter->addBinding('  hello  ', $model);

        $this->assertCount(1, $filter->queryLog);
        $this->assertSame(['hello'], $filter->queryLog[0]['params']);
    }

    public function test_add_binding_skips_empty_string(): void
    {
        $filter = new TestableEqual;
        $model = Mockery::mock(Model::class);

        $filter->addBinding('', $model);

        $this->assertCount(0, $filter->queryLog);
    }

    public function test_add_binding_skips_whitespace_only(): void
    {
        $filter = new TestableEqual;
        $model = Mockery::mock(Model::class);

        $filter->addBinding('   ', $model);

        $this->assertCount(0, $filter->queryLog);
    }

    public function test_extends_filter(): void
    {
        $filter = new Equal;

        $this->assertInstanceOf(Filter::class, $filter);
    }

    public function test_make_factory_method(): void
    {
        $filter = Equal::make('my placeholder');

        $this->assertInstanceOf(Equal::class, $filter);

        $ref = new \ReflectionProperty(Equal::class, 'placeholder');
        $ref->setAccessible(true);

        $this->assertSame('my placeholder', $ref->getValue($filter));
    }
}
