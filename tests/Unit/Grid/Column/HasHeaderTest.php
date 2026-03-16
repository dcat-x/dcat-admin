<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Grid\Column;

use Dcat\Admin\Grid;
use Dcat\Admin\Grid\Column;
use Dcat\Admin\Grid\Column\Filter;
use Dcat\Admin\Grid\Column\HasHeader;
use Dcat\Admin\Grid\Column\Help;
use Dcat\Admin\Grid\Column\Sorter;
use Dcat\Admin\Grid\Model;
use Dcat\Admin\Tests\TestCase;
use Mockery;

class HasHeaderTest extends TestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }

    protected function makeColumn(): Column
    {
        $column = new Column('name', 'Name');
        $column->setGrid(new FakeGridForHeader);

        return $column;
    }

    protected function getProtectedProperty(object $object, string $property)
    {
        $reflection = new \ReflectionProperty($object, $property);
        $reflection->setAccessible(true);

        return $reflection->getValue($object);
    }

    public function test_filter_property_is_public(): void
    {
        $reflection = new \ReflectionProperty(HasHeader::class, 'filter');

        $this->assertTrue($reflection->isPublic());
    }

    public function test_headers_default_empty_array(): void
    {
        $reflection = new \ReflectionProperty(HasHeader::class, 'headers');
        $reflection->setAccessible(true);

        $this->assertSame([], $reflection->getDefaultValue());
    }

    public function test_add_header_appends_content_and_returns_self(): void
    {
        $column = $this->makeColumn();

        $result = $column->addHeader('custom-header');

        $this->assertSame($column, $result);

        $headers = $this->getProtectedProperty($column, 'headers');
        $this->assertSame('custom-header', $headers[0]);
    }

    public function test_sortable_adds_sorter_header(): void
    {
        $column = $this->makeColumn();

        $result = $column->sortable();

        $this->assertSame($column, $result);

        $headers = $this->getProtectedProperty($column, 'headers');
        $this->assertInstanceOf(Sorter::class, $headers[0]);
    }

    public function test_filter_by_value_sets_hidden_filter_and_returns_self(): void
    {
        $column = $this->makeColumn();

        $result = $column->filterByValue('profile.name');

        $this->assertSame($column, $result);
        $this->assertInstanceOf(Filter::class, $column->filter);
        $this->assertFalse($column->filter->shouldDisplay());
    }

    public function test_help_adds_help_header(): void
    {
        $column = $this->makeColumn();

        $result = $column->help('Help text', 'blue', 'top');

        $this->assertSame($column, $result);

        $headers = $this->getProtectedProperty($column, 'headers');
        $this->assertInstanceOf(Help::class, $headers[0]);
    }

    public function test_bind_filter_query_invokes_filter_binding(): void
    {
        $column = $this->makeColumn();
        $column->filterByValue();

        $model = Mockery::mock(Model::class);

        $column->bindFilterQuery($model);

        $this->assertInstanceOf(Filter::class, $column->filter);
    }

    public function test_render_header_wraps_rendered_headers(): void
    {
        $column = $this->makeColumn();
        $column->addHeader('header-a')->addHeader('header-b');

        $rendered = $column->renderHeader();

        $this->assertStringContainsString('grid-column-header', $rendered);
        $this->assertStringContainsString('header-a', $rendered);
        $this->assertStringContainsString('header-b', $rendered);
    }
}

class FakeGridForHeader extends Grid
{
    public function __construct() {}

    public function listen($event, $callback = null)
    {
        return null;
    }

    public function makeName($name)
    {
        return $name;
    }
}
