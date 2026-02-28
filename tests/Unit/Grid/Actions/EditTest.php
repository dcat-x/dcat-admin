<?php

namespace Dcat\Admin\Tests\Unit\Grid\Actions;

use Dcat\Admin\Grid;
use Dcat\Admin\Grid\Actions\Edit;
use Dcat\Admin\Tests\TestCase;
use Illuminate\Support\Fluent;
use Mockery;

class EditTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    protected function makeAction($key = 1): Edit
    {
        $grid = Mockery::mock(Grid::class);
        $grid->shouldReceive('resource')->andReturn('/admin/users');
        $grid->shouldReceive('getKeyName')->andReturn('id');
        $grid->shouldReceive('getEditUrl')->andReturnUsing(function ($k) {
            return "/admin/users/{$k}/edit";
        });

        $action = new Edit;
        $action->setGrid($grid);

        $row = new Fluent(['id' => $key, 'name' => 'Test']);
        $action->setRow($row);

        return $action;
    }

    public function test_title_returns_default_with_icon(): void
    {
        $action = $this->makeAction();
        $title = $action->title();

        $this->assertStringContainsString('icon-edit', $title);
    }

    public function test_title_returns_custom_title_when_set(): void
    {
        $action = $this->makeAction();

        $ref = new \ReflectionProperty($action, 'title');
        $ref->setAccessible(true);
        $ref->setValue($action, 'Modify');

        $this->assertEquals('Modify', $action->title());
    }

    public function test_href_returns_edit_url_with_key(): void
    {
        $action = $this->makeAction(5);
        $href = $action->href();

        $this->assertEquals('/admin/users/5/edit', $href);
    }

    public function test_href_with_different_key(): void
    {
        $action = $this->makeAction(100);
        $href = $action->href();

        $this->assertEquals('/admin/users/100/edit', $href);
    }

    public function test_get_key_returns_row_key(): void
    {
        $action = $this->makeAction(77);
        $key = $action->getKey();

        $this->assertEquals(77, $key);
    }

    public function test_resource_returns_grid_resource(): void
    {
        $action = $this->makeAction();
        $resource = $action->resource();

        $this->assertEquals('/admin/users', $resource);
    }
}
