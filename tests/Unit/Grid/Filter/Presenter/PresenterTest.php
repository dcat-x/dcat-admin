<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Grid\Filter\Presenter;

use Dcat\Admin\Grid\Filter\AbstractFilter;
use Dcat\Admin\Grid\Filter\Presenter\Text;
use Dcat\Admin\Tests\TestCase;
use ReflectionProperty;

class PresenterTest extends TestCase
{
    protected function makePresenter(): Text
    {
        return new Text('Search...');
    }

    protected function makeFilterMock()
    {
        $filter = $this->createMock(AbstractFilter::class);
        $filter->method('column')->willReturn('test_column');

        return $filter;
    }

    public function test_set_parent_assigns_filter(): void
    {
        $presenter = $this->makePresenter();
        $filter = $this->makeFilterMock();

        $presenter->setParent($filter);

        $ref = new ReflectionProperty($presenter, 'filter');
        $ref->setAccessible(true);

        $this->assertSame($filter, $ref->getValue($presenter));
    }

    public function test_set_parent_calls_width_when_width_is_set(): void
    {
        $presenter = $this->makePresenter();

        $ref = new ReflectionProperty($presenter, 'width');
        $ref->setAccessible(true);
        $ref->setValue($presenter, 4);

        $filter = $this->makeFilterMock();
        $filter->expects($this->once())->method('width')->with(4);

        $presenter->setParent($filter);

        $this->assertTrue(true);
    }

    public function test_width_delegates_to_filter(): void
    {
        $presenter = $this->makePresenter();
        $filter = $this->makeFilterMock();
        $filter->expects($this->once())->method('width')->with(6);

        $presenter->setParent($filter);

        $result = $presenter->width(6);

        $this->assertSame($presenter, $result);
    }

    public function test_ignore_delegates_to_filter(): void
    {
        $presenter = $this->makePresenter();
        $filter = $this->makeFilterMock();
        $filter->expects($this->once())->method('ignore');

        $presenter->setParent($filter);

        $result = $presenter->ignore();

        $this->assertSame($presenter, $result);
    }

    public function test_default_delegates_to_filter(): void
    {
        $presenter = $this->makePresenter();
        $filter = $this->makeFilterMock();
        $filter->expects($this->once())->method('default')->with('default_val');

        $presenter->setParent($filter);

        $result = $presenter->default('default_val');

        $this->assertSame($presenter, $result);
    }

    public function test_value_returns_filter_value_when_set(): void
    {
        $presenter = $this->makePresenter();
        $filter = $this->makeFilterMock();
        $filter->method('getValue')->willReturn('search_term');
        $filter->method('getDefault')->willReturn(null);

        $presenter->setParent($filter);

        $this->assertSame('search_term', $presenter->value());
    }

    public function test_value_returns_default_when_value_is_null(): void
    {
        $presenter = $this->makePresenter();
        $filter = $this->makeFilterMock();
        $filter->method('getValue')->willReturn(null);
        $filter->method('getDefault')->willReturn('fallback');

        $presenter->setParent($filter);

        $this->assertSame('fallback', $presenter->value());
    }

    public function test_value_returns_default_when_value_is_empty_string(): void
    {
        $presenter = $this->makePresenter();
        $filter = $this->makeFilterMock();
        $filter->method('getValue')->willReturn('');
        $filter->method('getDefault')->willReturn('fallback');

        $presenter->setParent($filter);

        $this->assertSame('fallback', $presenter->value());
    }

    public function test_view_returns_default_view_name(): void
    {
        $presenter = $this->makePresenter();

        $this->assertSame('admin::filter.text', $presenter->view());
    }
}
