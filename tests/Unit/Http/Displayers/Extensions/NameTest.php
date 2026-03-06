<?php

namespace Dcat\Admin\Tests\Unit\Http\Displayers\Extensions;

use Dcat\Admin\Grid;
use Dcat\Admin\Grid\Column;
use Dcat\Admin\Grid\Displayers\AbstractDisplayer;
use Dcat\Admin\Http\Displayers\Extensions\Name;
use Dcat\Admin\Tests\TestCase;
use Mockery;

class NameTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    protected function makeDisplayer(string $value = 'demo'): TestableNameDisplayer
    {
        $grid = Mockery::mock(Grid::class);
        $column = Mockery::mock(Column::class);

        return new TestableNameDisplayer($value, $grid, $column, ['id' => 1]);
    }

    public function test_is_instance_of_abstract_displayer(): void
    {
        $displayer = $this->makeDisplayer();

        $this->assertInstanceOf(AbstractDisplayer::class, $displayer);
    }

    public function test_display_is_public_and_parameterless(): void
    {
        $method = new \ReflectionMethod(Name::class, 'display');

        $this->assertTrue($method->isPublic());
        $this->assertCount(0, $method->getParameters());
        $this->assertSame(Name::class, $method->getDeclaringClass()->getName());
    }

    public function test_resolve_action_is_protected_and_accepts_action_parameter(): void
    {
        $method = new \ReflectionMethod(Name::class, 'resolveAction');
        $params = $method->getParameters();

        $this->assertTrue($method->isProtected());
        $this->assertCount(1, $params);
        $this->assertSame('action', $params[0]->getName());
        $this->assertSame(Name::class, $method->getDeclaringClass()->getName());
    }

    public function test_resolve_action_sets_context_and_returns_action_rendered_content(): void
    {
        $displayer = $this->makeDisplayer('pkg');

        $result = $displayer->callResolveAction(FakeExtensionAction::class);

        $this->assertSame('rendered:1', $result);
    }

    public function test_class_is_not_abstract(): void
    {
        $reflection = new \ReflectionClass(Name::class);

        $this->assertFalse($reflection->isAbstract());
    }
}

class TestableNameDisplayer extends Name
{
    public function callResolveAction(string $action): string
    {
        return $this->resolveAction($action);
    }
}

class FakeExtensionAction
{
    protected $row;

    public function setGrid($grid): void {}

    public function setColumn($column): void {}

    public function setRow($row): void
    {
        $this->row = $row;
    }

    public function render(): string
    {
        return 'rendered:'.($this->row->id ?? '0');
    }
}
