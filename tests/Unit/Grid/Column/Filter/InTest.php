<?php

namespace Dcat\Admin\Tests\Unit\Grid\Column\Filter;

use Dcat\Admin\Grid\Column\Filter;
use Dcat\Admin\Grid\Column\Filter\Checkbox;
use Dcat\Admin\Grid\Column\Filter\In;
use Dcat\Admin\Grid\Model;
use Dcat\Admin\Tests\TestCase;
use Mockery;

class TestableIn extends In
{
    public $lastMethod = null;

    public $lastParams = null;

    protected function withQuery($model, string $query, array $params)
    {
        $this->lastMethod = $query;
        $this->lastParams = $params;
    }
}

class InTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_extends_filter(): void
    {
        $filter = new In(['a' => 'A', 'b' => 'B']);
        $this->assertInstanceOf(Filter::class, $filter);
    }

    public function test_constructor_stores_options(): void
    {
        $options = ['pending' => 'Pending', 'active' => 'Active'];
        $filter = new In($options);

        $ref = new \ReflectionProperty(In::class, 'options');
        $ref->setAccessible(true);
        $this->assertSame($options, $ref->getValue($filter));
    }

    public function test_constructor_creates_class_array(): void
    {
        $filter = new In(['a' => 'A']);

        $ref = new \ReflectionProperty(Filter::class, 'class');
        $ref->setAccessible(true);
        $class = $ref->getValue($filter);

        $this->assertIsArray($class);
        $this->assertStringStartsWith('column-filter-all-', $class['all'] ?? '');
        $this->assertStringStartsWith('column-filter-item-', $class['item'] ?? '');
    }

    public function test_uses_checkbox_trait(): void
    {
        $traits = class_uses(In::class);
        $this->assertContains(Checkbox::class, array_keys($traits));
    }

    public function test_add_binding_with_values(): void
    {
        $filter = new TestableIn(['a' => 'A', 'b' => 'B']);
        $filter->setColumnName('status');
        $model = Mockery::mock(Model::class);

        $filter->addBinding(['a', 'b'], $model);

        $this->assertSame('whereIn', $filter->lastMethod);
        $this->assertSame([['a', 'b']], $filter->lastParams);
    }

    public function test_add_binding_skips_empty_array(): void
    {
        $filter = new TestableIn(['a' => 'A']);
        $model = Mockery::mock(Model::class);

        $filter->addBinding([], $model);

        $this->assertNull($filter->lastMethod);
    }

    public function test_add_binding_uses_where_in(): void
    {
        $filter = new TestableIn(['x' => 'X']);
        $filter->setColumnName('type');
        $model = Mockery::mock(Model::class);

        $filter->addBinding(['x'], $model);

        $this->assertSame('whereIn', $filter->lastMethod);
    }

    public function test_make_factory_method(): void
    {
        $filter = In::make(['a' => 'A', 'b' => 'B']);
        $this->assertInstanceOf(In::class, $filter);
    }
}
