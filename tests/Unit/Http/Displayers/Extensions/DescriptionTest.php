<?php

namespace Dcat\Admin\Tests\Unit\Http\Displayers\Extensions;

use Dcat\Admin\Grid;
use Dcat\Admin\Grid\Column;
use Dcat\Admin\Grid\Displayers\AbstractDisplayer;
use Dcat\Admin\Http\Displayers\Extensions\Description;
use Dcat\Admin\Tests\TestCase;
use Mockery;

class DescriptionTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    protected function makeDisplayer(string $value = 'demo'): TestableDescriptionDisplayer
    {
        $grid = Mockery::mock(Grid::class);
        $column = Mockery::mock(Column::class);

        return new TestableDescriptionDisplayer($value, $grid, $column, ['id' => 1]);
    }

    public function test_is_instance_of_abstract_displayer(): void
    {
        $displayer = $this->makeDisplayer();

        $this->assertInstanceOf(AbstractDisplayer::class, $displayer);
    }

    public function test_display_method_signature_is_public_and_parameterless(): void
    {
        $method = new \ReflectionMethod(Description::class, 'display');

        $this->assertTrue($method->isPublic());
        $this->assertCount(0, $method->getParameters());
        $this->assertSame(Description::class, $method->getDeclaringClass()->getName());
    }

    public function test_resolve_setting_form_and_resolve_action_signatures_are_protected(): void
    {
        $resolveSettingForm = new \ReflectionMethod(Description::class, 'resolveSettingForm');
        $resolveAction = new \ReflectionMethod(Description::class, 'resolveAction');

        $this->assertTrue($resolveSettingForm->isProtected());
        $this->assertCount(0, $resolveSettingForm->getParameters());

        $this->assertTrue($resolveAction->isProtected());
        $this->assertCount(1, $resolveAction->getParameters());
        $this->assertSame('action', $resolveAction->getParameters()[0]->getName());
    }

    public function test_resolve_action_sets_context_and_returns_action_rendered_content(): void
    {
        $displayer = $this->makeDisplayer('pkg');

        $result = $displayer->callResolveAction(FakeDescriptionAction::class);

        $this->assertSame('rendered:1', $result);
    }

    public function test_get_modal_title_signature_accepts_extension_parameter(): void
    {
        $method = new \ReflectionMethod(Description::class, 'getModalTitle');

        $this->assertTrue($method->isProtected());
        $this->assertCount(1, $method->getParameters());
        $this->assertSame('extension', $method->getParameters()[0]->getName());
    }
}

class TestableDescriptionDisplayer extends Description
{
    public function callResolveAction(string $action): string
    {
        return $this->resolveAction($action);
    }
}

class FakeDescriptionAction
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
