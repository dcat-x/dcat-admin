<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Grid\Column\Filter;

use Dcat\Admin\Grid\Column\Filter\Equal;
use Dcat\Admin\Grid\Column\Filter\Nlt;
use Dcat\Admin\Grid\Model;
use Dcat\Admin\Tests\TestCase;
use Mockery;

class TestableNlt extends Nlt
{
    public array $queryLog = [];

    protected function withQuery($model, string $query, array $params): void
    {
        $this->queryLog[] = compact('query', 'params');
    }
}

class NltTest extends TestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }

    public function test_extends_equal(): void
    {
        $filter = new Nlt;

        $this->assertInstanceOf(Equal::class, $filter);
    }

    public function test_add_binding_uses_greater_than_or_equal_operator(): void
    {
        $filter = new TestableNlt;
        $model = Mockery::mock(Model::class);

        $filter->addBinding('100', $model);

        $this->assertCount(1, $filter->queryLog);
        $this->assertSame('where', $filter->queryLog[0]['query']);
        $this->assertSame(['>=', '100'], $filter->queryLog[0]['params']);
    }

    public function test_add_binding_skips_empty_string(): void
    {
        $filter = new TestableNlt;
        $model = Mockery::mock(Model::class);

        $filter->addBinding('', $model);

        $this->assertCount(0, $filter->queryLog);
    }

    public function test_add_binding_skips_whitespace_only(): void
    {
        $filter = new TestableNlt;
        $model = Mockery::mock(Model::class);

        $filter->addBinding('   ', $model);

        $this->assertCount(0, $filter->queryLog);
    }

    public function test_add_binding_trims_whitespace(): void
    {
        $filter = new TestableNlt;
        $model = Mockery::mock(Model::class);

        $filter->addBinding('  50  ', $model);

        $this->assertCount(1, $filter->queryLog);
        $this->assertSame(['>=', '50'], $filter->queryLog[0]['params']);
    }

    public function test_make_factory_method(): void
    {
        $filter = Nlt::make();

        $this->assertInstanceOf(Nlt::class, $filter);
    }
}
