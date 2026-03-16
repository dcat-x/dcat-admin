<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Form;

use Dcat\Admin\Actions\Action;
use Dcat\Admin\Form;
use Dcat\Admin\Form\AbstractTool;
use Dcat\Admin\Tests\TestCase;
use Mockery;

class FormAbstractToolTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    protected function createConcreteTool(): AbstractTool
    {
        return new class extends AbstractTool
        {
            public function html()
            {
                return '<button>Form Tool</button>';
            }
        };
    }

    public function test_tool_extends_action(): void
    {
        $tool = $this->createConcreteTool();
        $this->assertInstanceOf(Action::class, $tool);
    }

    public function test_set_form(): void
    {
        $tool = $this->createConcreteTool();
        $form = Mockery::mock(Form::class);

        $tool->setForm($form);

        $reflection = new \ReflectionProperty(AbstractTool::class, 'parent');
        $reflection->setAccessible(true);
        $this->assertSame($form, $reflection->getValue($tool));
    }

    public function test_get_key_returns_null_without_parent(): void
    {
        $tool = $this->createConcreteTool();
        $this->assertNull($tool->getKey());
    }

    public function test_get_key_delegates_to_parent(): void
    {
        $tool = $this->createConcreteTool();
        $form = Mockery::mock(Form::class);
        $form->shouldReceive('getKey')->andReturn(42);

        $tool->setForm($form);
        $this->assertSame(42, $tool->getKey());
    }

    public function test_get_key_returns_primary_key_when_set(): void
    {
        $tool = $this->createConcreteTool();
        $tool->setKey(99);

        $this->assertSame(99, $tool->getKey());
    }

    public function test_allow_only_creating_defaults_false(): void
    {
        $tool = $this->createConcreteTool();
        $this->assertFalse($tool->allowOnlyCreating);
    }

    public function test_allow_only_editing_defaults_false(): void
    {
        $tool = $this->createConcreteTool();
        $this->assertFalse($tool->allowOnlyEditing);
    }

    public function test_default_style(): void
    {
        $tool = $this->createConcreteTool();

        $reflection = new \ReflectionProperty(AbstractTool::class, 'style');
        $reflection->setAccessible(true);
        $this->assertSame('btn btn-sm btn-primary', $reflection->getValue($tool));
    }
}
