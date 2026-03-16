<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Grid\Tools;

use Dcat\Admin\Grid;
use Dcat\Admin\Grid\Tools;
use Dcat\Admin\Grid\Tools\CreateButton;
use Dcat\Admin\Tests\TestCase;
use Illuminate\Contracts\Support\Renderable;
use Mockery;

class CreateButtonTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    protected function getProtectedProperty(object $object, string $property)
    {
        $reflection = new \ReflectionProperty($object, $property);
        $reflection->setAccessible(true);

        return $reflection->getValue($object);
    }

    protected function invokeProtectedMethod(object $object, string $method, array $args = [])
    {
        $ref = new \ReflectionMethod($object, $method);
        $ref->setAccessible(true);

        return $ref->invokeArgs($object, $args);
    }

    protected function createMockGrid(array $options = []): Grid
    {
        $grid = Mockery::mock(Grid::class);
        $grid->shouldReceive('option')->with('create_mode')->andReturn($options['create_mode'] ?? null);
        $grid->shouldReceive('getKeyName')->andReturn('id');
        $grid->shouldReceive('getName')->andReturn('');
        $grid->shouldReceive('getCreateUrl')->andReturn('/admin/test/create');
        $grid->shouldReceive('makeName')->andReturnUsing(function ($key) {
            return 'grid-'.$key;
        });

        $tools = Mockery::mock(Tools::class);
        $tools->shouldReceive('format')->andReturnUsing(function ($html) {
            return $html;
        });
        $grid->shouldReceive('tools')->andReturn($tools);

        return $grid;
    }

    // -------------------------------------------------------------------------
    // Constructor
    // -------------------------------------------------------------------------

    public function test_constructor_stores_grid(): void
    {
        $grid = $this->createMockGrid();
        $button = new CreateButton($grid);

        $this->assertSame($grid, $this->getProtectedProperty($button, 'grid'));
    }

    public function test_constructor_stores_create_mode_from_grid_option(): void
    {
        $grid = $this->createMockGrid(['create_mode' => Grid::CREATE_MODE_DIALOG]);
        $button = new CreateButton($grid);

        $this->assertSame(Grid::CREATE_MODE_DIALOG, $this->getProtectedProperty($button, 'mode'));
    }

    public function test_constructor_stores_default_create_mode(): void
    {
        $grid = $this->createMockGrid(['create_mode' => Grid::CREATE_MODE_DEFAULT]);
        $button = new CreateButton($grid);

        $this->assertSame(Grid::CREATE_MODE_DEFAULT, $this->getProtectedProperty($button, 'mode'));
    }

    public function test_constructor_stores_null_mode_when_no_option(): void
    {
        $grid = $this->createMockGrid();
        $button = new CreateButton($grid);

        $this->assertNull($this->getProtectedProperty($button, 'mode'));
    }

    // -------------------------------------------------------------------------
    // renderCreateButton (protected)
    // -------------------------------------------------------------------------

    public function test_render_create_button_returns_link_when_mode_is_default(): void
    {
        $grid = $this->createMockGrid(['create_mode' => Grid::CREATE_MODE_DEFAULT]);
        $button = new CreateButton($grid);

        $html = $this->invokeProtectedMethod($button, 'renderCreateButton');

        $this->assertStringContainsString('/admin/test/create', $html);
        $this->assertStringContainsString('btn btn-primary', $html);
        $this->assertStringContainsString('<a href=', $html);
    }

    public function test_render_create_button_returns_link_when_mode_is_null(): void
    {
        $grid = $this->createMockGrid();
        $button = new CreateButton($grid);

        $html = $this->invokeProtectedMethod($button, 'renderCreateButton');

        $this->assertStringContainsString('/admin/test/create', $html);
        $this->assertStringContainsString('icon-plus', $html);
    }

    public function test_render_create_button_returns_null_when_mode_is_dialog(): void
    {
        $grid = $this->createMockGrid(['create_mode' => Grid::CREATE_MODE_DIALOG]);
        $button = new CreateButton($grid);

        $result = $this->invokeProtectedMethod($button, 'renderCreateButton');

        $this->assertNull($result);
    }

    // -------------------------------------------------------------------------
    // renderDialogCreateButton (protected)
    // -------------------------------------------------------------------------

    public function test_render_dialog_button_returns_null_when_mode_is_default(): void
    {
        $grid = $this->createMockGrid(['create_mode' => Grid::CREATE_MODE_DEFAULT]);
        $button = new CreateButton($grid);

        $result = $this->invokeProtectedMethod($button, 'renderDialogCreateButton');

        $this->assertNull($result);
    }

    public function test_render_dialog_button_returns_null_when_mode_is_null(): void
    {
        $grid = $this->createMockGrid();
        $button = new CreateButton($grid);

        $result = $this->invokeProtectedMethod($button, 'renderDialogCreateButton');

        $this->assertNull($result);
    }

    // -------------------------------------------------------------------------
    // render()
    // -------------------------------------------------------------------------

    public function test_render_returns_string_via_tools_format(): void
    {
        $grid = $this->createMockGrid(['create_mode' => Grid::CREATE_MODE_DEFAULT]);
        $button = new CreateButton($grid);

        $html = $button->render();

        $this->assertIsString($html);
        $this->assertStringContainsString('btn btn-primary', $html);
        $this->assertStringContainsString('/admin/test/create', $html);
    }

    public function test_render_contains_feather_icon_plus(): void
    {
        $grid = $this->createMockGrid(['create_mode' => Grid::CREATE_MODE_DEFAULT]);
        $button = new CreateButton($grid);

        $html = $button->render();

        $this->assertStringContainsString('feather icon-plus', $html);
    }

    // -------------------------------------------------------------------------
    // Interface
    // -------------------------------------------------------------------------

    public function test_implements_renderable(): void
    {
        $grid = $this->createMockGrid();
        $button = new CreateButton($grid);

        $this->assertInstanceOf(Renderable::class, $button);
    }
}
