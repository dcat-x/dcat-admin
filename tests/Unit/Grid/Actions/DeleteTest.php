<?php

namespace Dcat\Admin\Tests\Unit\Grid\Actions;

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

    public function test_class_exists(): void
    {
        $this->assertTrue(class_exists(Delete::class));
    }

    public function test_extends_row_action(): void
    {
        $action = Mockery::mock(Delete::class)->makePartial();

        $this->assertInstanceOf(RowAction::class, $action);
    }

    public function test_has_title_method(): void
    {
        $this->assertTrue(method_exists(Delete::class, 'title'));
    }

    public function test_title_contains_icon_trash(): void
    {
        $action = new Delete;
        $title = $action->title();

        $this->assertStringContainsString('icon-trash', $title);
    }

    public function test_title_contains_delete_text(): void
    {
        $action = new Delete;
        $title = $action->title();

        $this->assertStringContainsString('feather', $title);
    }

    public function test_title_returns_custom_when_set(): void
    {
        $action = new Delete;

        $ref = new \ReflectionProperty($action, 'title');
        $ref->setAccessible(true);
        $ref->setValue($action, 'Custom Delete');

        $this->assertEquals('Custom Delete', $action->title());
    }

    public function test_has_url_method(): void
    {
        $this->assertTrue(method_exists(Delete::class, 'url'));
    }

    public function test_has_render_method(): void
    {
        $this->assertTrue(method_exists(Delete::class, 'render'));
    }
}
