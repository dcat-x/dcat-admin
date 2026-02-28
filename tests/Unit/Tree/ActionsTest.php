<?php

namespace Dcat\Admin\Tests\Unit\Tree;

use Dcat\Admin\Tests\TestCase;
use Dcat\Admin\Tree\Actions;
use ReflectionProperty;

class ActionsTest extends TestCase
{
    protected function getProtectedProperty(object $object, string $property)
    {
        $reflection = new ReflectionProperty($object, $property);
        $reflection->setAccessible(true);

        return $reflection->getValue($object);
    }

    protected function setProtectedProperty(object $object, string $property, mixed $value): void
    {
        $reflection = new ReflectionProperty($object, $property);
        $reflection->setAccessible(true);
        $reflection->setValue($object, $value);
    }

    public function test_default_action_flags(): void
    {
        $actions = new Actions;
        $flags = $this->getProtectedProperty($actions, 'actions');

        $this->assertTrue($flags['delete']);
        $this->assertTrue($flags['quickEdit']);
        $this->assertFalse($flags['edit']);
    }

    public function test_append_adds_action_to_appends(): void
    {
        $actions = new Actions;
        $result = $actions->append('<button>Custom</button>');

        $this->assertSame($actions, $result);

        $appends = $this->getProtectedProperty($actions, 'appends');
        $this->assertCount(1, $appends);
        $this->assertEquals('<button>Custom</button>', $appends[0]);
    }

    public function test_append_multiple_actions_preserves_order(): void
    {
        $actions = new Actions;
        $actions->append('First');
        $actions->append('Second');
        $actions->append('Third');

        $appends = $this->getProtectedProperty($actions, 'appends');
        $this->assertCount(3, $appends);
        $this->assertEquals('First', $appends[0]);
        $this->assertEquals('Second', $appends[1]);
        $this->assertEquals('Third', $appends[2]);
    }

    public function test_prepend_adds_action_to_prepends(): void
    {
        $actions = new Actions;
        $actions->prepend('<button>First</button>');
        $actions->prepend('<button>Second</button>');

        $prepends = $this->getProtectedProperty($actions, 'prepends');

        $this->assertCount(2, $prepends);
        // prepend uses array_unshift, so most recent is first
        $this->assertEquals('<button>Second</button>', $prepends[0]);
        $this->assertEquals('<button>First</button>', $prepends[1]);
    }

    public function test_disable_delete(): void
    {
        $actions = new Actions;
        $result = $actions->disableDelete();

        $this->assertSame($actions, $result);

        $flags = $this->getProtectedProperty($actions, 'actions');
        $this->assertFalse($flags['delete']);
    }

    public function test_disable_delete_false_re_enables(): void
    {
        $actions = new Actions;
        $actions->disableDelete();
        $actions->disableDelete(false);

        $flags = $this->getProtectedProperty($actions, 'actions');
        $this->assertTrue($flags['delete']);
    }

    public function test_enable_edit(): void
    {
        $actions = new Actions;
        $result = $actions->edit();

        $this->assertSame($actions, $result);

        $flags = $this->getProtectedProperty($actions, 'actions');
        $this->assertTrue($flags['edit']);
    }

    public function test_disable_edit_disables_previously_enabled(): void
    {
        $actions = new Actions;
        $actions->edit(true);
        $actions->disableEdit();

        $flags = $this->getProtectedProperty($actions, 'actions');
        $this->assertFalse($flags['edit']);
    }

    public function test_disable_quick_edit(): void
    {
        $actions = new Actions;
        $result = $actions->disableQuickEdit();

        $this->assertSame($actions, $result);

        $flags = $this->getProtectedProperty($actions, 'actions');
        $this->assertFalse($flags['quickEdit']);
    }

    public function test_quick_edit_method_returns_self(): void
    {
        $actions = new Actions;
        $result = $actions->quickEdit(false);

        $this->assertSame($actions, $result);

        $flags = $this->getProtectedProperty($actions, 'actions');
        $this->assertFalse($flags['quickEdit']);
    }

    public function test_delete_method_with_false(): void
    {
        $actions = new Actions;
        $actions->delete(false);

        $flags = $this->getProtectedProperty($actions, 'actions');
        $this->assertFalse($flags['delete']);
    }

    public function test_set_and_get_row(): void
    {
        $actions = new Actions;
        $row = new \stdClass;
        $row->id = 1;
        $row->name = 'test';

        $actions->setRow($row);

        $this->assertSame($row, $actions->getRow());
        $this->assertSame($row, $actions->row);
    }

    public function test_set_and_get_parent(): void
    {
        $actions = new Actions;

        $tree = \Mockery::mock(\Dcat\Admin\Tree::class);
        $actions->setParent($tree);

        $this->assertSame($tree, $actions->parent());
    }

    public function test_default_actions_class_mapping(): void
    {
        $actions = new Actions;
        $defaultActions = $this->getProtectedProperty($actions, 'defaultActions');

        $this->assertArrayHasKey('edit', $defaultActions);
        $this->assertArrayHasKey('quickEdit', $defaultActions);
        $this->assertArrayHasKey('delete', $defaultActions);
        $this->assertEquals(\Dcat\Admin\Tree\Actions\Edit::class, $defaultActions['edit']);
        $this->assertEquals(\Dcat\Admin\Tree\Actions\QuickEdit::class, $defaultActions['quickEdit']);
        $this->assertEquals(\Dcat\Admin\Tree\Actions\Delete::class, $defaultActions['delete']);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        \Mockery::close();
    }
}
