<?php

namespace Dcat\Admin\Tests\Unit\Repositories;

use Dcat\Admin\Repositories\EloquentRepository;
use Dcat\Admin\Tests\TestCase;
use Illuminate\Database\Eloquent\Model;

class TestModel extends Model
{
    protected $table = 'test_table';

    protected $primaryKey = 'id';

    public $timestamps = true;
}

class TestTreeModel extends Model
{
    protected $table = 'test_tree_table';

    protected $primaryKey = 'id';

    public $timestamps = true;

    public function getParentColumn()
    {
        return 'parent_id';
    }

    public function getTitleColumn()
    {
        return 'title';
    }

    public function getOrderColumn()
    {
        return 'order';
    }
}

class TestRepository extends EloquentRepository
{
    protected $eloquentClass = TestModel::class;
}

class TestTreeRepository extends EloquentRepository
{
    protected $eloquentClass = TestTreeModel::class;
}

class RepositoryTest extends TestCase
{
    public function test_get_key_name_default(): void
    {
        $repo = new TestRepository;
        $this->assertEquals('id', $repo->getKeyName());
    }

    public function test_set_key_name(): void
    {
        $repo = new TestRepository;
        $repo->setKeyName('uuid');
        $this->assertEquals('uuid', $repo->getKeyName());
    }

    public function test_get_created_at_column(): void
    {
        $repo = new TestRepository;
        $this->assertEquals('created_at', $repo->getCreatedAtColumn());
    }

    public function test_get_updated_at_column(): void
    {
        $repo = new TestRepository;
        $this->assertEquals('updated_at', $repo->getUpdatedAtColumn());
    }

    public function test_is_soft_deletes_default(): void
    {
        $repo = new TestRepository;
        $this->assertFalse($repo->isSoftDeletes());
    }

    public function test_set_is_soft_deletes(): void
    {
        $repo = new TestRepository;
        $repo->setIsSoftDeletes(true);
        $this->assertTrue($repo->isSoftDeletes());
    }

    public function test_get_primary_key_column(): void
    {
        $repo = new TestRepository;
        $this->assertEquals('id', $repo->getPrimaryKeyColumn());
    }

    public function test_get_parent_column_with_tree_model(): void
    {
        $repo = new TestTreeRepository;
        $this->assertEquals('parent_id', $repo->getParentColumn());
    }

    public function test_get_title_column_with_tree_model(): void
    {
        $repo = new TestTreeRepository;
        $this->assertEquals('title', $repo->getTitleColumn());
    }

    public function test_get_order_column_with_tree_model(): void
    {
        $repo = new TestTreeRepository;
        $this->assertEquals('order', $repo->getOrderColumn());
    }

    public function test_get_parent_column_without_tree_model(): void
    {
        $repo = new TestRepository;
        // 当模型没有定义 getParentColumn 方法时返回 null
        $this->assertNull($repo->getParentColumn());
    }

    public function test_get_title_column_without_tree_model(): void
    {
        $repo = new TestRepository;
        // 当模型没有定义 getTitleColumn 方法时返回 null
        $this->assertNull($repo->getTitleColumn());
    }

    public function test_get_order_column_without_tree_model(): void
    {
        $repo = new TestRepository;
        // 当模型没有定义 getOrderColumn 方法时返回 null
        $this->assertNull($repo->getOrderColumn());
    }

    public function test_get_grid_columns(): void
    {
        $repo = new TestRepository;
        $this->assertEquals(['*'], $repo->getGridColumns());
    }

    public function test_get_form_columns(): void
    {
        $repo = new TestRepository;
        $this->assertEquals(['*'], $repo->getFormColumns());
    }

    public function test_get_detail_columns(): void
    {
        $repo = new TestRepository;
        $this->assertEquals(['*'], $repo->getDetailColumns());
    }

    public function test_set_relations(): void
    {
        $repo = new TestRepository;
        $repo->setRelations(['users', 'roles']);

        // 使用反射来检查关系是否设置
        $reflection = new \ReflectionClass($repo);
        $property = $reflection->getProperty('relations');
        $property->setAccessible(true);

        $this->assertEquals(['users', 'roles'], $property->getValue($repo));
    }
}
