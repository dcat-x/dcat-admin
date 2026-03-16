<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Grid\Actions;

use Dcat\Admin\Grid;
use Dcat\Admin\Grid\Actions\Delete;
use Dcat\Admin\Grid\RowAction;
use Dcat\Admin\Tests\TestCase;
use Mockery;

class DeleteTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_is_instance_of_row_action(): void
    {
        $action = new Delete;

        $this->assertInstanceOf(RowAction::class, $action);
    }

    public function test_title_contains_icon_and_delete_text_by_default(): void
    {
        $action = new Delete;
        $title = $action->title();

        $this->assertStringContainsString('icon-trash', $title);
        $this->assertStringContainsString('feather', $title);
    }

    public function test_title_returns_custom_when_set(): void
    {
        $action = new Delete;

        $ref = new \ReflectionProperty($action, 'title');
        $ref->setAccessible(true);
        $ref->setValue($action, 'Custom Delete');

        $this->assertSame('Custom Delete', $action->title());
    }

    public function test_url_uses_parent_resource_and_row_key(): void
    {
        $action = new Delete;

        $grid = Mockery::mock(Grid::class);
        $grid->shouldReceive('resource')->andReturn('/admin/users');
        $grid->shouldReceive('getKeyName')->andReturn('id');

        $action->setGrid($grid)->setRow((object) ['id' => 99]);

        $this->assertSame('/admin/users/99', $action->url());
    }

    public function test_render_sets_expected_data_attributes(): void
    {
        $action = new Delete;

        $grid = Mockery::mock(Grid::class);
        $model = Mockery::mock();
        $model->shouldReceive('withoutTreeQuery')->once()->andReturn('/admin/users?page=1');

        $grid->shouldReceive('resource')->andReturn('/admin/users');
        $grid->shouldReceive('getKeyName')->andReturn('id');
        $grid->shouldReceive('model')->andReturn($model);

        $action->setGrid($grid)->setRow((object) ['id' => 10]);

        $html = $action->render();

        $this->assertStringContainsString('data-action="delete"', $html);
        $this->assertStringContainsString('data-url="/admin/users/10"', $html);
        $this->assertStringContainsString('data-message="ID - 10"', $html);
        $this->assertStringContainsString('data-redirect="/admin/users?page=1"', $html);
    }

    public function test_url_and_render_method_signatures_are_public_and_parameterless(): void
    {
        $url = new \ReflectionMethod(Delete::class, 'url');
        $render = new \ReflectionMethod(Delete::class, 'render');

        $this->assertTrue($url->isPublic());
        $this->assertCount(0, $url->getParameters());

        $this->assertTrue($render->isPublic());
        $this->assertCount(0, $render->getParameters());
    }
}
