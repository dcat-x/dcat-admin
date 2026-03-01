<?php

namespace Dcat\Admin\Tests\Unit\Tree;

use Dcat\Admin\Actions\Action;
use Dcat\Admin\Tests\TestCase;
use Dcat\Admin\Tree;
use Dcat\Admin\Tree\Actions;
use Dcat\Admin\Tree\RowAction;
use Mockery;
use ReflectionProperty;

class RowActionTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    protected function getProtectedProperty(object $object, string $property)
    {
        $reflection = new ReflectionProperty($object, $property);
        $reflection->setAccessible(true);

        return $reflection->getValue($object);
    }

    protected function makeConcreteRowAction(): RowAction
    {
        return new class extends RowAction
        {
            public function render(): string
            {
                return '';
            }
        };
    }

    protected function makeActionsWithTree(string $resource = '/admin/categories', string $keyName = 'id'): array
    {
        $tree = Mockery::mock(Tree::class);
        $tree->shouldReceive('resource')->andReturn($resource);
        $tree->shouldReceive('getKeyName')->andReturn($keyName);

        $actions = Mockery::mock(Actions::class);
        $actions->shouldReceive('parent')->andReturn($tree);

        return [$actions, $tree];
    }

    public function test_extends_action(): void
    {
        $rowAction = $this->makeConcreteRowAction();

        $this->assertInstanceOf(Action::class, $rowAction);
    }

    public function test_set_row_and_get_row(): void
    {
        $rowAction = $this->makeConcreteRowAction();

        $row = new \stdClass;
        $row->id = 42;
        $row->name = 'Test Node';

        $rowAction->setRow($row);

        $this->assertSame($row, $rowAction->getRow());
    }

    public function test_set_parent_and_get_actions(): void
    {
        $rowAction = $this->makeConcreteRowAction();

        [$actions] = $this->makeActionsWithTree();
        $rowAction->setParent($actions);

        $this->assertSame($actions, $rowAction->getActions());
    }

    public function test_resource_delegates_to_actions_parent(): void
    {
        $rowAction = $this->makeConcreteRowAction();

        [$actions] = $this->makeActionsWithTree('/admin/categories');
        $rowAction->setParent($actions);

        $this->assertSame('/admin/categories', $rowAction->resource());
    }

    public function test_get_key_falls_back_to_row_when_primary_key_not_set(): void
    {
        $rowAction = $this->makeConcreteRowAction();

        $row = new \stdClass;
        $row->id = 99;

        [$actions] = $this->makeActionsWithTree('/admin/categories', 'id');
        $rowAction->setParent($actions);
        $rowAction->setRow($row);

        $this->assertSame(99, $rowAction->getKey());
    }

    public function test_get_key_returns_primary_key_when_set(): void
    {
        $rowAction = $this->makeConcreteRowAction();

        $rowAction->setKey(123);

        $this->assertSame(123, $rowAction->getKey());
    }

    public function test_get_key_uses_custom_key_name_from_tree(): void
    {
        $rowAction = $this->makeConcreteRowAction();

        $row = new \stdClass;
        $row->uuid = 'abc-def';

        [$actions] = $this->makeActionsWithTree('/admin/categories', 'uuid');
        $rowAction->setParent($actions);
        $rowAction->setRow($row);

        $this->assertSame('abc-def', $rowAction->getKey());
    }
}
