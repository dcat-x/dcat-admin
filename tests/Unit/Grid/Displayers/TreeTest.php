<?php

namespace Dcat\Admin\Tests\Unit\Grid\Displayers;

use Dcat\Admin\Grid;
use Dcat\Admin\Grid\Column;
use Dcat\Admin\Grid\Displayers\AbstractDisplayer;
use Dcat\Admin\Grid\Displayers\Tree;
use Dcat\Admin\Layout\Asset;
use Dcat\Admin\Tests\TestCase;
use Mockery;
use RuntimeException;

class TreeTest extends TestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }

    protected function makeDisplayer(array $options = []): Tree
    {
        $options = array_merge([
            'key' => 7,
            'value' => 'Node A',
            'children_count' => 2,
            'depth' => 1,
            'allow_pagination' => true,
            'show_all_children_nodes' => false,
            'per_page' => 15,
            'current_children_page' => 1,
            'last_page' => 3,
            'build_count' => 15,
        ], $options);

        $repositoryModel = new class($options)
        {
            public function __construct(private array $options) {}

            public function where($column, $value)
            {
                return $this;
            }

            public function count(): int
            {
                return $this->options['children_count'];
            }
        };

        $repository = new class($repositoryModel)
        {
            public function __construct(private object $model) {}

            public function getParentColumn(): string
            {
                return 'parent_id';
            }

            public function model(): object
            {
                return $this->model;
            }
        };

        $paginator = new class($options)
        {
            public function __construct(private array $options) {}

            public function lastPage(): int
            {
                return $this->options['last_page'];
            }
        };

        $model = new class($options, $repository, $paginator)
        {
            public function __construct(
                private array $options,
                private object $repository,
                private object $paginator
            ) {}

            public function getChildrenPageName($key): string
            {
                return 'children_page_'.$key;
            }

            public function showAllChildrenNodes(): bool
            {
                return $this->options['show_all_children_nodes'];
            }

            public function generateTreeUrl(): string
            {
                return 'http://localhost/admin/tree';
            }

            public function getPerPage(): int
            {
                return $this->options['per_page'];
            }

            public function getParentIdQueryName(): string
            {
                return 'parent_id';
            }

            public function getDepthQueryName(): string
            {
                return 'depth';
            }

            public function getDepthFromRequest(): int
            {
                return $this->options['depth'];
            }

            public function repository(): object
            {
                return $this->repository;
            }

            public function paginator(): object
            {
                return $this->paginator;
            }

            public function getCurrentChildrenPage(): int
            {
                return $this->options['current_children_page'];
            }

            public function buildData()
            {
                return collect(range(1, $this->options['build_count']));
            }
        };

        $grid = Mockery::mock(Grid::class);
        $grid->shouldReceive('getTableId')->andReturn('users');
        $grid->shouldReceive('model')->andReturn($model);
        $grid->shouldReceive('getKeyName')->andReturn('id');
        $grid->shouldReceive('allowPagination')->andReturn($options['allow_pagination']);

        $column = Mockery::mock(Column::class);
        $column->shouldReceive('getName')->andReturn('title');

        if (! $this->app->bound('admin.asset')) {
            $this->app->instance('admin.asset', new Asset);
        }

        return new class($options['value'], $grid, $column, ['id' => $options['key']]) extends Tree
        {
            public function exposeShowNextPage(): bool
            {
                return $this->showNextPage();
            }

            public function exposeResolveRepositoryModel($repository)
            {
                return $this->resolveRepositoryModel($repository);
            }

            public function exposeResolvePaginatorLastPage($paginator): int
            {
                return $this->resolvePaginatorLastPage($paginator);
            }
        };
    }

    public function test_display_renders_tree_link_with_key_and_value(): void
    {
        $displayer = $this->makeDisplayer([
            'key' => 11,
            'value' => 'Root Node',
        ]);

        $html = $displayer->display();

        $this->assertInstanceOf(AbstractDisplayer::class, $displayer);
        $this->assertStringContainsString('users-grid-load-children', $html);
        $this->assertStringContainsString('data-key="11"', $html);
        $this->assertStringContainsString('Root Node', $html);
        $this->assertStringContainsString('fa-angle-right', $html);
    }

    public function test_display_hides_expand_icon_when_no_children(): void
    {
        $displayer = $this->makeDisplayer([
            'children_count' => 0,
        ]);

        $html = $displayer->display();

        $this->assertStringNotContainsString('fa-angle-right', $html);
    }

    public function test_display_registers_tree_script_to_admin_asset(): void
    {
        $asset = new Asset;
        $this->app->instance('admin.asset', $asset);

        $displayer = $this->makeDisplayer();
        $displayer->display();

        $this->assertNotEmpty($asset->script);

        $script = implode("\n", $asset->script);
        $this->assertStringContainsString('Dcat.grid.Tree', $script);
        $this->assertStringContainsString("table: '#users'", $script);
    }

    public function test_show_next_page_returns_false_when_pagination_disabled(): void
    {
        $displayer = $this->makeDisplayer([
            'allow_pagination' => false,
        ]);

        $this->assertFalse($displayer->exposeShowNextPage());
    }

    public function test_show_next_page_returns_true_when_show_all_children_nodes_enabled(): void
    {
        $displayer = $this->makeDisplayer([
            'allow_pagination' => true,
            'show_all_children_nodes' => true,
        ]);

        $this->assertTrue($displayer->exposeShowNextPage());
    }

    public function test_show_next_page_returns_true_when_more_children_pages_exist(): void
    {
        $displayer = $this->makeDisplayer([
            'allow_pagination' => true,
            'show_all_children_nodes' => false,
            'current_children_page' => 1,
            'last_page' => 3,
            'build_count' => 15,
            'per_page' => 15,
        ]);

        $this->assertTrue($displayer->exposeShowNextPage());
    }

    public function test_show_next_page_returns_false_at_last_page(): void
    {
        $displayer = $this->makeDisplayer([
            'allow_pagination' => true,
            'show_all_children_nodes' => false,
            'current_children_page' => 3,
            'last_page' => 3,
            'build_count' => 15,
            'per_page' => 15,
        ]);

        $this->assertFalse($displayer->exposeShowNextPage());
    }

    public function test_show_next_page_returns_false_when_current_page_count_less_than_per_page(): void
    {
        $displayer = $this->makeDisplayer([
            'allow_pagination' => true,
            'show_all_children_nodes' => false,
            'current_children_page' => 1,
            'last_page' => 3,
            'build_count' => 10,
            'per_page' => 15,
        ]);

        $this->assertFalse($displayer->exposeShowNextPage());
    }

    public function test_resolve_repository_model_throws_exception_when_repository_has_no_model_method(): void
    {
        $displayer = $this->makeDisplayer();

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Repository must implement model() method.');

        $displayer->exposeResolveRepositoryModel(new class {});
    }

    public function test_resolve_paginator_last_page_returns_default_for_invalid_paginator(): void
    {
        $displayer = $this->makeDisplayer();

        $this->assertSame(1, $displayer->exposeResolvePaginatorLastPage(new class {}));
    }
}
