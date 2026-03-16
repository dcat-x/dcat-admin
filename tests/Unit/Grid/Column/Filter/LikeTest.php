<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Grid\Column\Filter;

use Dcat\Admin\Grid\Column\Filter\Equal;
use Dcat\Admin\Grid\Column\Filter\Like;
use Dcat\Admin\Grid\Model;
use Dcat\Admin\Tests\TestCase;
use Mockery;

class TestableLike extends Like
{
    public array $queryLog = [];

    protected function withQuery($model, string $query, array $params): void
    {
        $this->queryLog[] = compact('query', 'params');
    }
}

class LikeTest extends TestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }

    public function test_extends_equal(): void
    {
        $filter = new Like;

        $this->assertInstanceOf(Equal::class, $filter);
    }

    public function test_add_binding_uses_like_operator(): void
    {
        $filter = new TestableLike;
        $model = Mockery::mock(Model::class);

        $filter->addBinding('test', $model);

        $this->assertCount(1, $filter->queryLog);
        $this->assertSame('where', $filter->queryLog[0]['query']);
        $this->assertSame('like', $filter->queryLog[0]['params'][0]);
    }

    public function test_add_binding_wraps_value_with_wildcards(): void
    {
        $filter = new TestableLike;
        $model = Mockery::mock(Model::class);

        $filter->addBinding('search', $model);

        $this->assertSame(['like', '%search%'], $filter->queryLog[0]['params']);
    }

    public function test_add_binding_skips_empty_string(): void
    {
        $filter = new TestableLike;
        $model = Mockery::mock(Model::class);

        $filter->addBinding('', $model);

        $this->assertCount(0, $filter->queryLog);
    }

    public function test_add_binding_skips_whitespace_only(): void
    {
        $filter = new TestableLike;
        $model = Mockery::mock(Model::class);

        $filter->addBinding('   ', $model);

        $this->assertCount(0, $filter->queryLog);
    }

    public function test_add_binding_trims_whitespace(): void
    {
        $filter = new TestableLike;
        $model = Mockery::mock(Model::class);

        $filter->addBinding('  hello  ', $model);

        $this->assertCount(1, $filter->queryLog);
        $this->assertSame(['like', '%hello%'], $filter->queryLog[0]['params']);
    }

    public function test_make_factory_method(): void
    {
        $filter = Like::make('placeholder');

        $this->assertInstanceOf(Like::class, $filter);
    }
}
