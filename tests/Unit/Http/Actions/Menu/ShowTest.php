<?php

namespace Dcat\Admin\Tests\Unit\Http\Actions\Menu;

use Dcat\Admin\Http\Actions\Menu\Show;
use Dcat\Admin\Tests\TestCase;
use Dcat\Admin\Tree\RowAction;
use Illuminate\Support\Fluent;

class ShowTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        ShowMenuFakeModel::reset();
        $this->app['config']->set('admin.database.menu_model', ShowMenuFakeModel::class);
    }

    public function test_title_switches_icon_based_on_row_show_flag(): void
    {
        $action = new Show;
        $action->setRow(new Fluent(['show' => 1]));

        $this->assertInstanceOf(RowAction::class, $action);
        $this->assertStringContainsString('icon-eye-off', $action->title());

        $action->setRow(new Fluent(['show' => 0]));
        $this->assertStringContainsString('icon-eye', $action->title());
    }

    public function test_handle_toggles_menu_show_status_and_returns_location_response(): void
    {
        $action = new Show;
        $action->setKey(9);

        $response = $action->handle()->toArray();

        $this->assertSame(9, ShowMenuFakeModel::$lastFindId);
        $this->assertSame(['show' => 0], ShowMenuFakeModel::$lastUpdateData);
        $this->assertTrue($response['status']);
        $this->assertSame('location', $response['data']['then']['action']);
        $this->assertStringContainsString('auth/menu', $response['data']['then']['value']);
    }
}

class ShowMenuFakeModel
{
    public static ?int $lastFindId = null;

    public static ?array $lastUpdateData = null;

    public int $show = 1;

    public static function reset(): void
    {
        static::$lastFindId = null;
        static::$lastUpdateData = null;
    }

    public static function find($id): self
    {
        static::$lastFindId = (int) $id;

        return new self;
    }

    public function update(array $data): bool
    {
        static::$lastUpdateData = $data;

        return true;
    }
}
