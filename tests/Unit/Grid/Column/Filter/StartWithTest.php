<?php

namespace Dcat\Admin\Tests\Unit\Grid\Column\Filter;

use Dcat\Admin\Grid\Column\Filter\Equal;
use Dcat\Admin\Grid\Column\Filter\StartWith;
use Dcat\Admin\Grid\Model;
use Dcat\Admin\Tests\TestCase;
use Mockery;

class TestableStartWith extends StartWith
{
    public array $queryLog = [];

    protected function withQuery($model, string $query, array $params): void
    {
        $this->queryLog[] = compact('query', 'params');
    }
}

class StartWithTest extends TestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }

    public function test_extends_equal(): void
    {
        $filter = new StartWith;

        $this->assertInstanceOf(Equal::class, $filter);
    }

    public function test_add_binding_uses_like_operator(): void
    {
        $filter = new TestableStartWith;
        $model = Mockery::mock(Model::class);

        $filter->addBinding('test', $model);

        $this->assertCount(1, $filter->queryLog);
        $this->assertSame('where', $filter->queryLog[0]['query']);
        $this->assertSame('like', $filter->queryLog[0]['params'][0]);
    }

    public function test_add_binding_appends_wildcard_to_value(): void
    {
        $filter = new TestableStartWith;
        $model = Mockery::mock(Model::class);

        $filter->addBinding('prefix', $model);

        $this->assertSame(['like', 'prefix%'], $filter->queryLog[0]['params']);
    }

    public function test_add_binding_skips_empty_string(): void
    {
        $filter = new TestableStartWith;
        $model = Mockery::mock(Model::class);

        $filter->addBinding('', $model);

        $this->assertCount(0, $filter->queryLog);
    }

    public function test_add_binding_skips_whitespace_only(): void
    {
        $filter = new TestableStartWith;
        $model = Mockery::mock(Model::class);

        $filter->addBinding('   ', $model);

        $this->assertCount(0, $filter->queryLog);
    }

    public function test_add_binding_trims_whitespace(): void
    {
        $filter = new TestableStartWith;
        $model = Mockery::mock(Model::class);

        $filter->addBinding('  hello  ', $model);

        $this->assertCount(1, $filter->queryLog);
        $this->assertSame(['like', 'hello%'], $filter->queryLog[0]['params']);
    }

    public function test_make_factory_method(): void
    {
        $filter = StartWith::make();

        $this->assertInstanceOf(StartWith::class, $filter);
    }
}
