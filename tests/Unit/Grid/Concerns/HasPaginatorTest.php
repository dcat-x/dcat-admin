<?php

namespace Dcat\Admin\Tests\Unit\Grid\Concerns;

use Dcat\Admin\Grid\Concerns\HasPaginator;
use Dcat\Admin\Tests\TestCase;
use Mockery;

class HasPaginatorTestHelper
{
    use HasPaginator;

    public $options = [
        'pagination' => true,
        'paginator_class' => null,
    ];

    public $model;

    public function __construct()
    {
        $this->model = Mockery::mock();
        $this->model->shouldReceive('setPerPage')->byDefault();
        $this->model->shouldReceive('usePaginate')->byDefault();
        $this->model->shouldReceive('getSortName')->andReturn('_sort')->byDefault();
        $this->model->shouldReceive('simple')->byDefault();
    }

    public function model()
    {
        return $this->model;
    }

    public function option($key, $value = null)
    {
        if ($value !== null) {
            $this->options[$key] = $value;

            return $this;
        }

        return $this->options[$key] ?? null;
    }
}

class HasPaginatorTest extends TestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }

    protected function createHelper(): HasPaginatorTestHelper
    {
        return new HasPaginatorTestHelper;
    }

    public function test_default_per_page_is_20(): void
    {
        $helper = $this->createHelper();

        $this->assertSame(20, $helper->getPerPage());
    }

    public function test_default_per_pages_array(): void
    {
        $helper = $this->createHelper();

        $this->assertSame([10, 20, 30, 50, 100, 200], $helper->getPerPages());
    }

    public function test_get_per_page(): void
    {
        $helper = $this->createHelper();

        $this->assertSame(20, $helper->getPerPage());
    }

    public function test_per_pages_sets_array(): void
    {
        $helper = $this->createHelper();
        $result = $helper->perPages([5, 10, 25]);

        $this->assertSame($helper, $result);
        $this->assertSame([5, 10, 25], $helper->getPerPages());
    }

    public function test_get_per_pages_returns_array(): void
    {
        $helper = $this->createHelper();
        $helper->perPages([15, 30, 60]);

        $this->assertIsArray($helper->getPerPages());
        $this->assertSame([15, 30, 60], $helper->getPerPages());
    }

    public function test_disable_per_pages_sets_empty_array(): void
    {
        $helper = $this->createHelper();
        $helper->disablePerPages();

        $this->assertSame([], $helper->getPerPages());
    }

    public function test_allow_pagination_reads_options(): void
    {
        $helper = $this->createHelper();

        $this->assertTrue($helper->allowPagination());
    }

    public function test_paginate_sets_per_page(): void
    {
        $helper = $this->createHelper();
        $helper->model->shouldReceive('setPerPage')->once()->with(50);

        $helper->paginate(50);

        $this->assertSame(50, $helper->getPerPage());
    }

    public function test_show_pagination_enables(): void
    {
        $helper = $this->createHelper();
        $helper->model->shouldReceive('usePaginate')->with(false);
        $helper->disablePagination();

        $helper->model->shouldReceive('usePaginate')->with(true);
        $helper->showPagination();

        $this->assertTrue($helper->options['pagination']);
    }

    public function test_disable_pagination_disables(): void
    {
        $helper = $this->createHelper();
        $helper->model->shouldReceive('usePaginate')->once()->with(false);

        $helper->disablePagination();

        $this->assertFalse($helper->options['pagination']);
    }
}
