<?php

namespace Dcat\Admin\Tests\Unit\Contracts;

use Dcat\Admin\Contracts\TreeRepository;
use Dcat\Admin\Tests\TestCase;
use Mockery;

class TreeRepositoryTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_interface_exists(): void
    {
        $this->assertTrue(interface_exists(TreeRepository::class));
    }

    public function test_anonymous_class_implements_interface(): void
    {
        $instance = $this->makeTreeRepository();

        $this->assertInstanceOf(TreeRepository::class, $instance);
    }

    public function test_get_primary_key_column_method_exists(): void
    {
        $reflection = new \ReflectionClass(TreeRepository::class);

        $this->assertTrue($reflection->hasMethod('getPrimaryKeyColumn'));
    }

    public function test_get_parent_column_method_exists(): void
    {
        $reflection = new \ReflectionClass(TreeRepository::class);

        $this->assertTrue($reflection->hasMethod('getParentColumn'));
    }

    public function test_get_title_column_method_exists(): void
    {
        $reflection = new \ReflectionClass(TreeRepository::class);

        $this->assertTrue($reflection->hasMethod('getTitleColumn'));
    }

    public function test_get_order_column_method_exists(): void
    {
        $reflection = new \ReflectionClass(TreeRepository::class);

        $this->assertTrue($reflection->hasMethod('getOrderColumn'));
    }

    public function test_save_order_method_exists(): void
    {
        $reflection = new \ReflectionClass(TreeRepository::class);

        $this->assertTrue($reflection->hasMethod('saveOrder'));
    }

    public function test_with_query_method_exists(): void
    {
        $reflection = new \ReflectionClass(TreeRepository::class);

        $this->assertTrue($reflection->hasMethod('withQuery'));
    }

    public function test_to_tree_method_exists(): void
    {
        $reflection = new \ReflectionClass(TreeRepository::class);

        $this->assertTrue($reflection->hasMethod('toTree'));
    }

    public function test_interface_has_exactly_seven_methods(): void
    {
        $reflection = new \ReflectionClass(TreeRepository::class);

        $this->assertCount(7, $reflection->getMethods());
    }

    public function test_save_order_has_default_parameters(): void
    {
        $reflection = new \ReflectionMethod(TreeRepository::class, 'saveOrder');
        $params = $reflection->getParameters();

        $this->assertCount(2, $params);
        $this->assertSame('tree', $params[0]->getName());
        $this->assertTrue($params[0]->isDefaultValueAvailable());
        $this->assertSame([], $params[0]->getDefaultValue());

        $this->assertSame('parentId', $params[1]->getName());
        $this->assertTrue($params[1]->isDefaultValueAvailable());
        $this->assertSame(0, $params[1]->getDefaultValue());
    }

    public function test_with_query_accepts_one_parameter(): void
    {
        $reflection = new \ReflectionMethod(TreeRepository::class, 'withQuery');
        $params = $reflection->getParameters();

        $this->assertCount(1, $params);
        $this->assertSame('queryCallback', $params[0]->getName());
    }

    public function test_get_primary_key_column_returns_string(): void
    {
        $instance = $this->makeTreeRepository();

        $this->assertIsString($instance->getPrimaryKeyColumn());
        $this->assertSame('id', $instance->getPrimaryKeyColumn());
    }

    public function test_get_parent_column_returns_string(): void
    {
        $instance = $this->makeTreeRepository();

        $this->assertIsString($instance->getParentColumn());
        $this->assertSame('parent_id', $instance->getParentColumn());
    }

    public function test_get_title_column_returns_string(): void
    {
        $instance = $this->makeTreeRepository();

        $this->assertIsString($instance->getTitleColumn());
        $this->assertSame('title', $instance->getTitleColumn());
    }

    public function test_get_order_column_returns_string(): void
    {
        $instance = $this->makeTreeRepository();

        $this->assertIsString($instance->getOrderColumn());
        $this->assertSame('order', $instance->getOrderColumn());
    }

    public function test_to_tree_returns_array(): void
    {
        $instance = $this->makeTreeRepository();

        $this->assertIsArray($instance->toTree());
    }

    public function test_with_query_returns_self(): void
    {
        $instance = $this->makeTreeRepository();

        $result = $instance->withQuery(function () {});

        $this->assertSame($instance, $result);
    }

    protected function makeTreeRepository(): TreeRepository
    {
        return new class implements TreeRepository
        {
            protected ?\Closure $queryCallback = null;

            public function getPrimaryKeyColumn()
            {
                return 'id';
            }

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

            public function saveOrder($tree = [], $parentId = 0)
            {
                // no-op for testing
            }

            public function withQuery($queryCallback)
            {
                $this->queryCallback = $queryCallback;

                return $this;
            }

            public function toTree()
            {
                return [];
            }
        };
    }
}
