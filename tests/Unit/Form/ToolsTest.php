<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Form;

use Dcat\Admin\Form;
use Dcat\Admin\Form\Builder;
use Dcat\Admin\Form\Tools;
use Dcat\Admin\Tests\TestCase;
use Mockery;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;

#[AllowMockObjectsWithoutExpectations]
class ToolsTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    protected function createTools(): Tools
    {
        $builder = Mockery::mock(Builder::class);
        $builder->shouldReceive('resource')->andReturn('/admin/users');
        $builder->shouldReceive('getResourceId')->andReturn(1);
        $builder->shouldReceive('isCreating')->andReturn(false);
        $builder->shouldReceive('form')->andReturn(Mockery::mock(Form::class));

        return new Tools($builder);
    }

    protected function createMockBuilder(): Builder
    {
        $builder = Mockery::mock(Builder::class);
        $builder->shouldReceive('resource')->andReturn('/admin/users');
        $builder->shouldReceive('getResourceId')->andReturn(1);
        $builder->shouldReceive('isCreating')->andReturn(false);
        $builder->shouldReceive('form')->andReturn(Mockery::mock(Form::class));

        return $builder;
    }

    public function test_constructor_creates_instance(): void
    {
        $tools = $this->createTools();

        $this->assertInstanceOf(Tools::class, $tools);
    }

    public function test_append_returns_this(): void
    {
        $tools = $this->createTools();
        $result = $tools->append('custom tool');

        $this->assertSame($tools, $result);
    }

    public function test_prepend_returns_this(): void
    {
        $tools = $this->createTools();
        $result = $tools->prepend('custom tool');

        $this->assertSame($tools, $result);
    }

    public function test_disable_list(): void
    {
        $tools = $this->createTools();
        $tools->disableList();

        $ref = new \ReflectionProperty($tools, 'tools');
        $ref->setAccessible(true);
        $toolsArray = $ref->getValue($tools);

        $this->assertFalse($toolsArray['list']);
    }

    public function test_disable_list_with_false_re_enables(): void
    {
        $tools = $this->createTools();
        $tools->disableList();
        $tools->disableList(false);

        $ref = new \ReflectionProperty($tools, 'tools');
        $ref->setAccessible(true);
        $toolsArray = $ref->getValue($tools);

        $this->assertTrue($toolsArray['list']);
    }

    public function test_disable_delete(): void
    {
        $tools = $this->createTools();
        $tools->disableDelete();

        $ref = new \ReflectionProperty($tools, 'tools');
        $ref->setAccessible(true);
        $toolsArray = $ref->getValue($tools);

        $this->assertFalse($toolsArray['delete']);
    }

    public function test_disable_delete_with_false_re_enables(): void
    {
        $tools = $this->createTools();
        $tools->disableDelete();
        $tools->disableDelete(false);

        $ref = new \ReflectionProperty($tools, 'tools');
        $ref->setAccessible(true);
        $toolsArray = $ref->getValue($tools);

        $this->assertTrue($toolsArray['delete']);
    }

    public function test_disable_view(): void
    {
        $tools = $this->createTools();
        $tools->disableView();

        $ref = new \ReflectionProperty($tools, 'tools');
        $ref->setAccessible(true);
        $toolsArray = $ref->getValue($tools);

        $this->assertFalse($toolsArray['view']);
    }

    public function test_disable_view_with_false_re_enables(): void
    {
        $tools = $this->createTools();
        $tools->disableView();
        $tools->disableView(false);

        $ref = new \ReflectionProperty($tools, 'tools');
        $ref->setAccessible(true);
        $toolsArray = $ref->getValue($tools);

        $this->assertTrue($toolsArray['view']);
    }

    public function test_form_returns_builder(): void
    {
        $builder = $this->createMockBuilder();
        $tools = new Tools($builder);

        $this->assertSame($builder, $tools->form());
    }

    public function test_default_tools_all_enabled(): void
    {
        $tools = $this->createTools();

        $ref = new \ReflectionProperty($tools, 'tools');
        $ref->setAccessible(true);
        $toolsArray = $ref->getValue($tools);

        $this->assertTrue($toolsArray['delete']);
        $this->assertTrue($toolsArray['view']);
        $this->assertTrue($toolsArray['list']);
    }

    public function test_render_contains_list_button(): void
    {
        $tools = $this->createTools();
        $html = $tools->render();

        $this->assertStringContainsString('/admin/users', $html);
        $this->assertStringContainsString('icon-list', $html);
    }
}
