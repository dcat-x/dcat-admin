<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Repositories;

use Dcat\Admin\Contracts\TreeRepository;
use Dcat\Admin\Exception\RuntimeException;
use Dcat\Admin\Repositories\QueryBuilderRepository;
use Dcat\Admin\Repositories\Repository;
use Dcat\Admin\Tests\TestCase;
use Mockery;

class TestQueryBuilderRepository extends QueryBuilderRepository
{
    protected $table = 'test_qb_table';
}

class TestQueryBuilderRepositoryWithConnection extends QueryBuilderRepository
{
    protected $table = 'test_qb_table';

    protected $connection = 'testing';
}

class TestQueryBuilderRepositoryCustomTimestamps extends QueryBuilderRepository
{
    protected $table = 'test_qb_table';

    protected $createdAtColumn = 'date_created';

    protected $updatedAtColumn = 'date_updated';
}

class QueryBuilderRepositoryTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    protected function setUp(): void
    {
        parent::setUp();

        // Create a test table in the SQLite in-memory database
        $this->app['db']->connection()->getSchemaBuilder()->create('test_qb_table', function ($table) {
            $table->increments('id');
            $table->string('name')->nullable();
            $table->string('slug')->nullable();
            $table->timestamps();
        });
    }

    public function test_extends_repository(): void
    {
        $repo = new TestQueryBuilderRepository;
        $this->assertInstanceOf(Repository::class, $repo);
    }

    public function test_implements_tree_repository(): void
    {
        $repo = new TestQueryBuilderRepository;
        $this->assertInstanceOf(TreeRepository::class, $repo);
    }

    public function test_get_table_returns_configured_table(): void
    {
        $repo = new TestQueryBuilderRepository;
        $this->assertSame('test_qb_table', $repo->getTable());
    }

    public function test_get_created_at_column_returns_default(): void
    {
        $repo = new TestQueryBuilderRepository;
        $this->assertSame('created_at', $repo->getCreatedAtColumn());
    }

    public function test_get_updated_at_column_returns_default(): void
    {
        $repo = new TestQueryBuilderRepository;
        $this->assertSame('updated_at', $repo->getUpdatedAtColumn());
    }

    public function test_get_created_at_column_returns_custom(): void
    {
        $repo = new TestQueryBuilderRepositoryCustomTimestamps;
        $this->assertSame('date_created', $repo->getCreatedAtColumn());
    }

    public function test_get_updated_at_column_returns_custom(): void
    {
        $repo = new TestQueryBuilderRepositoryCustomTimestamps;
        $this->assertSame('date_updated', $repo->getUpdatedAtColumn());
    }

    public function test_get_grid_columns_returns_wildcard(): void
    {
        $repo = new TestQueryBuilderRepository;
        $this->assertSame(['*'], $repo->getGridColumns());
    }

    public function test_get_form_columns_returns_wildcard(): void
    {
        $repo = new TestQueryBuilderRepository;
        $this->assertSame(['*'], $repo->getFormColumns());
    }

    public function test_get_detail_columns_returns_wildcard(): void
    {
        $repo = new TestQueryBuilderRepository;
        $this->assertSame(['*'], $repo->getDetailColumns());
    }

    public function test_get_key_name_defaults_to_id(): void
    {
        $repo = new TestQueryBuilderRepository;
        $this->assertSame('id', $repo->getKeyName());
    }

    public function test_move_order_up_throws_runtime_exception(): void
    {
        $this->expectException(RuntimeException::class);

        $repo = new TestQueryBuilderRepository;
        $repo->moveOrderUp();
    }

    public function test_move_order_down_throws_runtime_exception(): void
    {
        $this->expectException(RuntimeException::class);

        $repo = new TestQueryBuilderRepository;
        $repo->moveOrderDown();
    }

    public function test_get_parent_column_throws_runtime_exception(): void
    {
        $this->expectException(RuntimeException::class);

        $repo = new TestQueryBuilderRepository;
        $repo->getParentColumn();
    }

    public function test_get_title_column_throws_runtime_exception(): void
    {
        $this->expectException(RuntimeException::class);

        $repo = new TestQueryBuilderRepository;
        $repo->getTitleColumn();
    }

    public function test_get_order_column_throws_runtime_exception(): void
    {
        $this->expectException(RuntimeException::class);

        $repo = new TestQueryBuilderRepository;
        $repo->getOrderColumn();
    }

    public function test_save_order_throws_runtime_exception(): void
    {
        $this->expectException(RuntimeException::class);

        $repo = new TestQueryBuilderRepository;
        $repo->saveOrder([], 0);
    }

    public function test_with_query_throws_runtime_exception(): void
    {
        $this->expectException(RuntimeException::class);

        $repo = new TestQueryBuilderRepository;
        $repo->withQuery(function () {});
    }

    public function test_to_tree_throws_runtime_exception(): void
    {
        $this->expectException(RuntimeException::class);

        $repo = new TestQueryBuilderRepository;
        $repo->toTree();
    }

    public function test_new_query_returns_clone_of_query_builder(): void
    {
        $repo = new TestQueryBuilderRepository;
        $method = new \ReflectionMethod($repo, 'newQuery');
        $method->setAccessible(true);

        $query1 = $method->invoke($repo);
        $query2 = $method->invoke($repo);

        // Each call returns a clone, so they should not be the same instance
        $this->assertNotSame($query1, $query2);
    }

    public function test_store_inserts_and_returns_id(): void
    {
        $repo = new TestQueryBuilderRepository;

        $form = Mockery::mock(\Dcat\Admin\Form::class);
        $form->shouldReceive('updates')->andReturn([
            'name' => 'Test Name',
            'slug' => 'test-slug',
        ]);

        $result = $repo->store($form);

        $this->assertIsInt($result);
        $this->assertGreaterThan(0, $result);
    }

    public function test_edit_returns_array_for_existing_record(): void
    {
        // Insert a record first
        $this->app['db']->connection()->table('test_qb_table')->insert([
            'name' => 'Test',
            'slug' => 'test',
        ]);

        $repo = new TestQueryBuilderRepository;

        $form = Mockery::mock(\Dcat\Admin\Form::class);
        $form->shouldReceive('getKey')->andReturn(1);

        $result = $repo->edit($form);

        $this->assertIsArray($result);
        $this->assertSame('Test', $result['name']);
        $this->assertSame('test', $result['slug']);
    }

    public function test_update_modifies_existing_record(): void
    {
        // Insert a record first
        $this->app['db']->connection()->table('test_qb_table')->insert([
            'name' => 'Original',
            'slug' => 'original',
        ]);

        $repo = new TestQueryBuilderRepository;

        $form = Mockery::mock(\Dcat\Admin\Form::class);
        $form->shouldReceive('getKey')->andReturn(1);
        $form->shouldReceive('updates')->andReturn([
            'name' => 'Updated',
        ]);

        $result = $repo->update($form);

        $this->assertSame(1, $result);

        // Verify the update
        $record = $this->app['db']->connection()->table('test_qb_table')->find(1);
        $this->assertSame('Updated', $record->name);
    }

    public function test_deleting_returns_records_for_deletion(): void
    {
        $this->app['db']->connection()->table('test_qb_table')->insert([
            'name' => 'ToDelete1',
            'slug' => 'delete1',
        ]);
        $this->app['db']->connection()->table('test_qb_table')->insert([
            'name' => 'ToDelete2',
            'slug' => 'delete2',
        ]);

        $repo = new TestQueryBuilderRepository;

        $form = Mockery::mock(\Dcat\Admin\Form::class);
        $form->shouldReceive('getKey')->andReturn('1,2');

        $result = $repo->deleting($form);

        $this->assertIsArray($result);
        $this->assertCount(2, $result);
        $this->assertSame('ToDelete1', $result[0]['name']);
        $this->assertSame('ToDelete2', $result[1]['name']);
    }

    public function test_delete_removes_records(): void
    {
        $this->app['db']->connection()->table('test_qb_table')->insert([
            'name' => 'ToDelete',
            'slug' => 'to-delete',
        ]);

        $repo = new TestQueryBuilderRepository;

        $form = Mockery::mock(\Dcat\Admin\Form::class);
        $form->shouldReceive('getKey')->andReturn('1');
        $form->shouldReceive('deleteFiles')->with(Mockery::any());

        $deletingData = [['id' => 1, 'name' => 'ToDelete', 'slug' => 'to-delete']];

        $result = $repo->delete($form, $deletingData);

        $this->assertTrue($result);

        // Verify deletion
        $record = $this->app['db']->connection()->table('test_qb_table')->find(1);
        $this->assertNull($record);
    }

    public function test_detail_returns_array_for_existing_record(): void
    {
        $this->app['db']->connection()->table('test_qb_table')->insert([
            'name' => 'DetailTest',
            'slug' => 'detail-test',
        ]);

        $repo = new TestQueryBuilderRepository;

        // Cannot directly mock Show due to __call signature issue, use anonymous class
        $show = new class(1, new TestQueryBuilderRepository) extends \Dcat\Admin\Show
        {
            protected $testKey;

            public function __construct($key, $repository)
            {
                $this->testKey = $key;
            }

            public function getKey()
            {
                return $this->testKey;
            }
        };

        $result = $repo->detail($show);

        $this->assertIsArray($result);
        $this->assertSame('DetailTest', $result['name']);
    }

    public function test_updating_delegates_to_edit(): void
    {
        $this->app['db']->connection()->table('test_qb_table')->insert([
            'name' => 'UpdatingTest',
            'slug' => 'updating-test',
        ]);

        $repo = new TestQueryBuilderRepository;

        $form = Mockery::mock(\Dcat\Admin\Form::class);
        $form->shouldReceive('getKey')->andReturn(1);

        $result = $repo->updating($form);

        $this->assertIsArray($result);
        $this->assertSame('UpdatingTest', $result['name']);
    }

    public function test_constructor_initializes_query_builder(): void
    {
        $repo = new TestQueryBuilderRepository;

        $reflection = new \ReflectionProperty($repo, 'queryBuilder');
        $reflection->setAccessible(true);

        $this->assertNotNull($reflection->getValue($repo));
    }

    public function test_is_soft_deletes_default_false(): void
    {
        $repo = new TestQueryBuilderRepository;
        $this->assertFalse($repo->isSoftDeletes());
    }

    public function test_make_static_factory(): void
    {
        $repo = TestQueryBuilderRepository::make();
        $this->assertInstanceOf(TestQueryBuilderRepository::class, $repo);
    }
}
