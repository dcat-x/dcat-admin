<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Grid\Tools;

use Dcat\Admin\Grid;
use Dcat\Admin\Grid\Model;
use Dcat\Admin\Grid\Tools\QuickSearch;
use Dcat\Admin\Tests\TestCase;
use Mockery;

class QuickSearchTest extends TestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }

    protected function createQuickSearch(): QuickSearch
    {
        $grid = Mockery::mock(Grid::class);
        $grid->shouldReceive('makeName')->andReturnUsing(function ($key) {
            return 'grid-'.$key;
        });

        $model = Mockery::mock(Model::class);
        $model->shouldReceive('getPageName')->andReturn('grid-page');
        $grid->shouldReceive('model')->andReturn($model);
        $grid->shouldReceive('resource')->andReturn('/admin/users');

        $search = new QuickSearch;
        $search->setGrid($grid);

        return $search;
    }

    public function test_get_query_name_returns_prefixed_name(): void
    {
        $search = $this->createQuickSearch();

        $this->assertSame('grid-_search_', $search->getQueryName());
    }

    public function test_width_sets_value_and_returns_this(): void
    {
        $search = $this->createQuickSearch();

        $result = $search->width(25);

        $this->assertSame($search, $result);

        $ref = new \ReflectionProperty($search, 'width');
        $ref->setAccessible(true);
        $this->assertSame(25, $ref->getValue($search));
    }

    public function test_default_width_is_18(): void
    {
        $search = $this->createQuickSearch();

        $ref = new \ReflectionProperty($search, 'width');
        $ref->setAccessible(true);
        $this->assertSame(18, $ref->getValue($search));
    }

    public function test_placeholder_sets_text_and_returns_this(): void
    {
        $search = $this->createQuickSearch();

        $result = $search->placeholder('Search users...');

        $this->assertSame($search, $result);

        $ref = new \ReflectionProperty($search, 'placeholder');
        $ref->setAccessible(true);
        $this->assertSame('Search users...', $ref->getValue($search));
    }

    public function test_default_placeholder_is_null(): void
    {
        $search = $this->createQuickSearch();

        $ref = new \ReflectionProperty($search, 'placeholder');
        $ref->setAccessible(true);
        $this->assertNull($ref->getValue($search));
    }

    public function test_value_returns_trimmed_request_value(): void
    {
        $search = $this->createQuickSearch();

        // Without any request data, value should be empty string
        $this->assertSame('', $search->value());
    }

    public function test_auto_sets_auto_submit_true(): void
    {
        $search = $this->createQuickSearch();

        $result = $search->auto(true);

        $this->assertSame($search, $result);

        $ref = new \ReflectionProperty($search, 'autoSubmit');
        $ref->setAccessible(true);
        $this->assertTrue($ref->getValue($search));
    }

    public function test_auto_sets_auto_submit_false(): void
    {
        $search = $this->createQuickSearch();

        $search->auto(false);

        $ref = new \ReflectionProperty($search, 'autoSubmit');
        $ref->setAccessible(true);
        $this->assertFalse($ref->getValue($search));
    }

    public function test_default_auto_submit_is_true(): void
    {
        $search = $this->createQuickSearch();

        $ref = new \ReflectionProperty($search, 'autoSubmit');
        $ref->setAccessible(true);
        $this->assertTrue($ref->getValue($search));
    }

    public function test_view_is_quick_search(): void
    {
        $search = $this->createQuickSearch();

        $ref = new \ReflectionProperty($search, 'view');
        $ref->setAccessible(true);
        $this->assertSame('admin::grid.quick-search', $ref->getValue($search));
    }

    public function test_default_query_name(): void
    {
        $search = $this->createQuickSearch();

        $ref = new \ReflectionProperty($search, 'queryName');
        $ref->setAccessible(true);
        $this->assertSame('_search_', $ref->getValue($search));
    }

    public function test_fluent_api_chaining(): void
    {
        $search = $this->createQuickSearch();

        $result = $search
            ->width(20)
            ->placeholder('Type here...')
            ->auto(false);

        $this->assertSame($search, $result);

        $refWidth = new \ReflectionProperty($search, 'width');
        $refWidth->setAccessible(true);
        $this->assertSame(20, $refWidth->getValue($search));

        $refPlaceholder = new \ReflectionProperty($search, 'placeholder');
        $refPlaceholder->setAccessible(true);
        $this->assertSame('Type here...', $refPlaceholder->getValue($search));

        $refAuto = new \ReflectionProperty($search, 'autoSubmit');
        $refAuto->setAccessible(true);
        $this->assertFalse($refAuto->getValue($search));
    }
}
