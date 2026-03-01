<?php

namespace Dcat\Admin\Tests\Unit\Grid\Column;

use Dcat\Admin\Grid\Column;
use Dcat\Admin\Grid\Column\Filter;
use Dcat\Admin\Grid\Model;
use Dcat\Admin\Tests\TestCase;
use Illuminate\Contracts\Support\Renderable;
use Mockery;

class ConcreteFilter extends Filter
{
    public function __construct()
    {
        $this->class = 'test-class';
    }

    public function render()
    {
        return 'rendered';
    }
}

class FilterTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_implements_renderable(): void
    {
        $filter = new ConcreteFilter;
        $this->assertInstanceOf(Renderable::class, $filter);
    }

    public function test_set_column_name_stores_name(): void
    {
        $filter = new ConcreteFilter;
        $result = $filter->setColumnName('test_column');

        $this->assertSame($filter, $result);

        $ref = new \ReflectionProperty(Filter::class, 'columnName');
        $ref->setAccessible(true);
        $this->assertEquals('test_column', $ref->getValue($filter));
    }

    public function test_get_column_name_replaces_dot_with_underscore(): void
    {
        $filter = new ConcreteFilter;
        $filter->setColumnName('relation.column');

        $this->assertEquals('relation_column', $filter->getColumnName());
    }

    public function test_get_column_name_replaces_arrow_with_underscore(): void
    {
        $filter = new ConcreteFilter;
        $filter->setColumnName('json->key');

        $this->assertEquals('json_key', $filter->getColumnName());
    }

    public function test_get_original_column_name_returns_set_name(): void
    {
        $filter = new ConcreteFilter;
        $filter->setColumnName('my_column');

        $this->assertEquals('my_column', $filter->getOriginalColumnName());
    }

    public function test_get_original_column_name_falls_back_to_parent(): void
    {
        $filter = new ConcreteFilter;

        $column = Mockery::mock(Column::class);
        $column->shouldReceive('getName')->andReturn('parent_column');

        $ref = new \ReflectionProperty(Filter::class, 'parent');
        $ref->setAccessible(true);
        $ref->setValue($filter, $column);

        $this->assertEquals('parent_column', $filter->getOriginalColumnName());
    }

    public function test_display_sets_display_true(): void
    {
        $filter = new ConcreteFilter;
        $filter->hide();
        $result = $filter->display(true);

        $this->assertSame($filter, $result);
        $this->assertTrue($filter->shouldDisplay());
    }

    public function test_display_sets_display_false(): void
    {
        $filter = new ConcreteFilter;
        $result = $filter->display(false);

        $this->assertSame($filter, $result);
        $this->assertFalse($filter->shouldDisplay());
    }

    public function test_hide_sets_display_false(): void
    {
        $filter = new ConcreteFilter;
        $result = $filter->hide();

        $this->assertSame($filter, $result);
        $this->assertFalse($filter->shouldDisplay());
    }

    public function test_should_display_default_true(): void
    {
        $filter = new ConcreteFilter;
        $this->assertTrue($filter->shouldDisplay());
    }

    public function test_should_display_after_hide(): void
    {
        $filter = new ConcreteFilter;
        $filter->hide();
        $this->assertFalse($filter->shouldDisplay());
    }

    public function test_resolving_stores_closure(): void
    {
        $filter = new ConcreteFilter;
        $closure = function () {};
        $result = $filter->resolving($closure);

        $this->assertSame($filter, $result);

        $ref = new \ReflectionProperty(Filter::class, 'resolvings');
        $ref->setAccessible(true);
        $resolvings = $ref->getValue($filter);

        $this->assertCount(1, $resolvings);
        $this->assertSame($closure, $resolvings[0]);
    }

    public function test_resolving_stores_multiple_closures(): void
    {
        $filter = new ConcreteFilter;
        $closure1 = function () {};
        $closure2 = function () {};

        $filter->resolving($closure1);
        $filter->resolving($closure2);

        $ref = new \ReflectionProperty(Filter::class, 'resolvings');
        $ref->setAccessible(true);
        $resolvings = $ref->getValue($filter);

        $this->assertCount(2, $resolvings);
    }

    public function test_make_creates_instance(): void
    {
        $filter = ConcreteFilter::make();
        $this->assertInstanceOf(ConcreteFilter::class, $filter);
    }

    public function test_add_binding_does_nothing_by_default(): void
    {
        $filter = new ConcreteFilter;
        $model = Mockery::mock(Model::class);

        // addBinding in base class is empty - should not throw
        $filter->addBinding('value', $model);
        $this->assertTrue(true);
    }

    public function test_render_returns_rendered_string(): void
    {
        $filter = new ConcreteFilter;
        $this->assertEquals('rendered', $filter->render());
    }

    public function test_parent_returns_null_by_default(): void
    {
        $filter = new ConcreteFilter;
        $this->assertNull($filter->parent());
    }
}
