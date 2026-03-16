<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Grid;

use Dcat\Admin\Grid;
use Dcat\Admin\Grid\Model;
use Dcat\Admin\Tests\TestCase;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Mockery;

class ModelTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    protected function createMockGrid(): Grid
    {
        $grid = Mockery::mock(Grid::class);
        $grid->shouldReceive('makeName')->andReturnUsing(function ($name) {
            return $name;
        });
        $grid->shouldReceive('getKeyName')->andReturn('id');

        return $grid;
    }

    protected function createModel(?Grid $grid = null): Model
    {
        $request = new Request;
        $model = new Model($request);
        if ($grid) {
            $model->setGrid($grid);
        }

        return $model;
    }

    // 1. Constructor creates instance with Request
    public function test_constructor_creates_instance_with_request(): void
    {
        $model = $this->createModel();

        $this->assertInstanceOf(Model::class, $model);
    }

    // 2. getQueries() returns unique collection
    public function test_get_queries_returns_unique_collection(): void
    {
        $model = $this->createModel();

        $model->addQuery('where', ['id', '=', 1]);
        $model->addQuery('where', ['id', '=', 1]);

        $queries = $model->getQueries();

        $this->assertInstanceOf(Collection::class, $queries);
        $this->assertCount(1, $queries);
    }

    // 3. setQueries() replaces queries
    public function test_set_queries_replaces_queries(): void
    {
        $model = $this->createModel();

        $model->addQuery('where', ['name', 'foo']);

        $newQueries = new Collection([
            ['method' => 'orderBy', 'arguments' => ['id']],
        ]);
        $model->setQueries($newQueries);

        $queries = $model->getQueries();
        $this->assertCount(1, $queries);
        $this->assertSame('orderBy', $queries->first()['method']);
    }

    // 4. simple() sets simple pagination mode
    public function test_simple_sets_simple_pagination_mode(): void
    {
        $model = $this->createModel();

        $result = $model->simple();

        $this->assertSame($model, $result);
        $this->assertSame('simplePaginate', $model->getPaginateMethod());
    }

    // 5. getPaginateMethod() returns 'paginate' or 'simplePaginate'
    public function test_get_paginate_method_returns_paginate_by_default(): void
    {
        $model = $this->createModel();

        $this->assertSame('paginate', $model->getPaginateMethod());
    }

    public function test_get_paginate_method_returns_simple_paginate_when_simple(): void
    {
        $model = $this->createModel();
        $model->simple(true);

        $this->assertSame('simplePaginate', $model->getPaginateMethod());
    }

    // 6. usePaginate() enables/disables pagination
    public function test_use_paginate_enables_and_disables_pagination(): void
    {
        $model = $this->createModel();

        $this->assertTrue($model->allowPagination());

        $result = $model->usePaginate(false);
        $this->assertSame($model, $result);
        $this->assertFalse($model->allowPagination());

        $model->usePaginate(true);
        $this->assertTrue($model->allowPagination());
    }

    // 7. allowPagination() returns usePaginate state
    public function test_allow_pagination_returns_use_paginate_state(): void
    {
        $model = $this->createModel();

        $this->assertTrue($model->allowPagination());

        $model->usePaginate(false);
        $this->assertFalse($model->allowPagination());
    }

    // 8. setPerPage() and getPerPage() work correctly
    public function test_set_per_page_and_get_per_page(): void
    {
        $grid = $this->createMockGrid();
        $model = $this->createModel($grid);

        $result = $model->setPerPage(50);
        $this->assertSame($model, $result);
        $this->assertSame(50, $model->getPerPage());
    }

    public function test_get_per_page_returns_null_when_pagination_disabled(): void
    {
        $model = $this->createModel();
        $model->usePaginate(false);

        $this->assertNull($model->getPerPage());
    }

    // 9. setPageName() and getPageName()
    public function test_set_page_name_and_get_page_name(): void
    {
        $grid = $this->createMockGrid();
        $model = $this->createModel($grid);

        $result = $model->setPageName('p');
        $this->assertSame($model, $result);
        $this->assertSame('p', $model->getPageName());
    }

    // 10. setSortName() and getSortName()
    public function test_set_sort_name_and_get_sort_name(): void
    {
        $grid = $this->createMockGrid();
        $model = $this->createModel($grid);

        $result = $model->setSortName('sort');
        $this->assertSame($model, $result);
        $this->assertSame('sort', $model->getSortName());
    }

    // 11. addQuery() pushes to queries collection
    public function test_add_query_pushes_to_queries_collection(): void
    {
        $model = $this->createModel();

        $result = $model->addQuery('where', ['status', 'active']);
        $this->assertSame($model, $result);

        $queries = $model->getQueries();
        $this->assertCount(1, $queries);
        $this->assertSame('where', $queries->first()['method']);
        $this->assertSame(['status', 'active'], $queries->first()['arguments']);
    }

    // 12. findQueryByMethod() filters queries
    public function test_find_query_by_method_filters_queries(): void
    {
        $model = $this->createModel();

        $model->addQuery('where', ['id', 1]);
        $model->addQuery('orderBy', ['name']);
        $model->addQuery('where', ['status', 'active']);

        $whereQueries = $model->findQueryByMethod('where');
        $this->assertCount(2, $whereQueries);

        $orderQueries = $model->findQueryByMethod('orderBy');
        $this->assertCount(1, $orderQueries);

        $emptyQueries = $model->findQueryByMethod('groupBy');
        $this->assertCount(0, $emptyQueries);
    }

    // 13. filterQueryBy() with string method name
    public function test_filter_query_by_with_string_method_name(): void
    {
        $model = $this->createModel();

        $model->addQuery('where', ['id', 1]);
        $model->addQuery('orderBy', ['name']);
        $model->addQuery('where', ['status', 'active']);

        $result = $model->filterQueryBy('where');
        $this->assertSame($model, $result);

        $queries = $model->getQueries();
        $this->assertCount(1, $queries);
        $this->assertSame('orderBy', $queries->first()['method']);
    }

    // 14. rejectQuery() removes matching queries
    public function test_reject_query_removes_matching_queries(): void
    {
        $model = $this->createModel();

        $model->addQuery('where', ['id', 1]);
        $model->addQuery('orderBy', ['name']);
        $model->addQuery('where', ['status', 'active']);

        $model->rejectQuery('where');

        $queries = $model->getQueries();
        $this->assertCount(1, $queries);
        $this->assertSame('orderBy', $queries->first()['method']);
    }

    public function test_reject_query_with_array_of_methods(): void
    {
        $model = $this->createModel();

        $model->addQuery('where', ['id', 1]);
        $model->addQuery('orderBy', ['name']);
        $model->addQuery('orderByDesc', ['created_at']);

        $model->rejectQuery(['orderBy', 'orderByDesc']);

        $queries = $model->getQueries();
        $this->assertCount(1, $queries);
        $this->assertSame('where', $queries->first()['method']);
    }

    // 15. resetOrderBy() rejects orderBy/orderByDesc
    public function test_reset_order_by_rejects_order_queries(): void
    {
        $model = $this->createModel();

        $model->addQuery('where', ['id', 1]);
        $model->addQuery('orderBy', ['name']);
        $model->addQuery('orderByDesc', ['created_at']);

        $model->resetOrderBy();

        $queries = $model->getQueries();
        $this->assertCount(1, $queries);
        $this->assertSame('where', $queries->first()['method']);
    }

    // 16. getSort() returns [null,null,null] when no sort
    public function test_get_sort_returns_null_array_when_no_sort(): void
    {
        $grid = $this->createMockGrid();
        $model = $this->createModel($grid);

        $sort = $model->getSort();

        $this->assertSame([null, null, null], $sort);
    }

    // 17. setData() with Collection
    public function test_set_data_with_collection(): void
    {
        $model = $this->createModel();

        $data = new Collection([
            ['id' => 1, 'name' => 'Alice'],
            ['id' => 2, 'name' => 'Bob'],
        ]);

        $result = $model->setData($data);
        $this->assertSame($model, $result);

        $builtData = $model->buildData();
        $this->assertInstanceOf(Collection::class, $builtData);
        $this->assertCount(2, $builtData);
    }

    // 18. setData() with array
    public function test_set_data_with_array(): void
    {
        $model = $this->createModel();

        $data = [
            ['id' => 1, 'name' => 'Alice'],
            ['id' => 2, 'name' => 'Bob'],
        ];

        $model->setData($data);

        $builtData = $model->buildData();
        $this->assertInstanceOf(Collection::class, $builtData);
        $this->assertCount(2, $builtData);
    }

    // 19. setData() with callable stores builder
    public function test_set_data_with_callable_stores_builder(): void
    {
        $model = $this->createModel();
        $called = false;

        $model->setData(function ($m) use (&$called) {
            $called = true;

            return new Collection([['id' => 1]]);
        });

        // Builder should not be called immediately
        $this->assertFalse($called);

        // Builder is called when data is built
        $data = $model->buildData();
        $this->assertTrue($called);
        $this->assertCount(1, $data);
    }

    // 20. setGrid() and grid() work correctly
    public function test_set_grid_and_grid(): void
    {
        $model = $this->createModel();
        $grid = $this->createMockGrid();

        $result = $model->setGrid($grid);
        $this->assertSame($model, $result);
        $this->assertSame($grid, $model->grid());
    }

    // 21. setConstraints() and getConstraints()
    public function test_set_constraints_and_get_constraints(): void
    {
        $model = $this->createModel();

        $this->assertSame([], $model->getConstraints());

        $constraints = ['user_id' => 1, 'type' => 'admin'];
        $result = $model->setConstraints($constraints);

        $this->assertSame($model, $result);
        $this->assertSame($constraints, $model->getConstraints());
    }

    // 22. getSortQueries() finds all sort-related queries
    public function test_get_sort_queries_finds_all_sort_related_queries(): void
    {
        $model = $this->createModel();

        $model->addQuery('where', ['id', 1]);
        $model->addQuery('orderBy', ['name', 'asc']);
        $model->addQuery('orderByDesc', ['created_at']);
        $model->addQuery('latest', []);
        $model->addQuery('oldest', []);
        $model->addQuery('limit', [10]);

        $sortQueries = $model->getSortQueries();

        $this->assertCount(4, $sortQueries);

        $methods = $sortQueries->pluck('method')->values()->all();
        $this->assertContains('orderBy', $methods);
        $this->assertContains('orderByDesc', $methods);
        $this->assertContains('latest', $methods);
        $this->assertContains('oldest', $methods);
    }

    // 23. getSortDescMethods() returns correct methods
    public function test_get_sort_desc_methods_returns_correct_methods(): void
    {
        $model = $this->createModel();

        $methods = $model->getSortDescMethods();

        $this->assertSame(['orderByDesc', 'latest'], $methods);
    }

    // 24. with() handles string relations
    public function test_with_handles_string_relations(): void
    {
        $model = $this->createModel();

        $result = $model->with('comments');

        $this->assertSame($model, $result);

        $queries = $model->getQueries();
        $withQueries = $queries->where('method', 'with');
        $this->assertCount(1, $withQueries);
    }

    public function test_with_handles_string_relation_with_dot_notation(): void
    {
        $model = $this->createModel();

        $model->with('comments.author');

        $queries = $model->findQueryByMethod('with');
        $this->assertCount(1, $queries);
    }

    public function test_with_handles_string_relation_with_colon(): void
    {
        $model = $this->createModel();

        $model->with('comments:id,body');

        $queries = $model->findQueryByMethod('with');
        $this->assertCount(1, $queries);
    }

    public function test_with_does_not_duplicate_same_relation(): void
    {
        $model = $this->createModel();

        $model->with('comments');
        $model->with('comments');

        // The second call returns early without adding a duplicate eager load,
        // but still adds a query entry. The eagerLoads array should only have one.
        // We verify through the query collection that the 'with' query is added.
        $queries = $model->findQueryByMethod('with');
        $this->assertCount(1, $queries);
    }

    // 25. with() handles array relations
    public function test_with_handles_array_relations(): void
    {
        $model = $this->createModel();

        $model->with(['comments', 'tags']);

        $queries = $model->findQueryByMethod('with');
        $this->assertCount(1, $queries);
    }

    // 26. reset() clears data and queries
    public function test_reset_clears_data_and_queries(): void
    {
        $model = $this->createModel();

        $model->setData(new Collection([['id' => 1]]));
        $model->addQuery('where', ['id', 1]);

        $model->reset();

        $queries = $model->getQueries();
        $this->assertCount(0, $queries);
    }

    // Additional: __call delegates to addQuery
    public function test_magic_call_delegates_to_add_query(): void
    {
        $model = $this->createModel();

        $model->where('id', 1);

        $queries = $model->getQueries();
        $this->assertCount(1, $queries);
        $this->assertSame('where', $queries->first()['method']);
        $this->assertSame(['id', 1], $queries->first()['arguments']);
    }

    // Additional: filterQueryBy with array of method names
    public function test_filter_query_by_with_array_of_methods(): void
    {
        $model = $this->createModel();

        $model->addQuery('where', ['id', 1]);
        $model->addQuery('orderBy', ['name']);
        $model->addQuery('orderByDesc', ['created_at']);
        $model->addQuery('limit', [10]);

        $model->filterQueryBy(['orderBy', 'orderByDesc']);

        $queries = $model->getQueries();
        $this->assertCount(2, $queries);

        $methods = $queries->pluck('method')->values()->all();
        $this->assertContains('where', $methods);
        $this->assertContains('limit', $methods);
    }

    // Additional: simple(false) reverts to normal pagination
    public function test_simple_false_reverts_to_normal_pagination(): void
    {
        $model = $this->createModel();

        $model->simple(true);
        $this->assertSame('simplePaginate', $model->getPaginateMethod());

        $model->simple(false);
        $this->assertSame('paginate', $model->getPaginateMethod());
    }

    // Additional: getSort with sort data in request
    public function test_get_sort_returns_sort_data_from_request(): void
    {
        $grid = $this->createMockGrid();
        $request = new Request([
            '_sort' => ['column' => 'name', 'type' => 'desc'],
        ]);
        $model = new Model($request);
        $model->setGrid($grid);

        $sort = $model->getSort();

        $this->assertSame(['name', 'desc', null], $sort);
    }

    public function test_get_sort_returns_cast_when_present(): void
    {
        $grid = $this->createMockGrid();
        $request = new Request([
            '_sort' => ['column' => 'price', 'type' => 'asc', 'cast' => 'int'],
        ]);
        $model = new Model($request);
        $model->setGrid($grid);

        $sort = $model->getSort();

        $this->assertSame(['price', 'asc', 'int'], $sort);
    }

    // Additional: getPerPage reads from request
    public function test_get_per_page_reads_from_request(): void
    {
        $grid = $this->createMockGrid();
        $request = new Request(['per_page' => '50']);
        $model = new Model($request);
        $model->setGrid($grid);

        $this->assertSame(50, $model->getPerPage());
    }

    // Additional: default per page is 20
    public function test_get_per_page_defaults_to_twenty(): void
    {
        $grid = $this->createMockGrid();
        $model = $this->createModel($grid);

        $this->assertSame(20, $model->getPerPage());
    }

    // Additional: rejectQuery with callable
    public function test_reject_query_with_callable(): void
    {
        $model = $this->createModel();

        $model->addQuery('where', ['id', 1]);
        $model->addQuery('orderBy', ['name']);
        $model->addQuery('limit', [10]);

        $model->rejectQuery(function ($query) {
            return $query['method'] === 'limit';
        });

        $queries = $model->getQueries();
        $this->assertCount(2, $queries);

        $methods = $queries->pluck('method')->values()->all();
        $this->assertNotContains('limit', $methods);
    }
}
