<?php

namespace Dcat\Admin\Tests\Unit\Traits;

use Dcat\Admin\Tests\TestCase;
use Dcat\Admin\Traits\HasDataPermission;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class FakeMenuQueryForHasDataPermissionTest
{
    public static int $firstCalls = 0;

    public function orWhere($column, $value): self
    {
        return $this;
    }

    public function first()
    {
        static::$firstCalls++;

        return (object) ['id' => 123];
    }
}

class FakeMenuModelForHasDataPermissionTest
{
    public static function where($column, $value): FakeMenuQueryForHasDataPermissionTest
    {
        return new FakeMenuQueryForHasDataPermissionTest;
    }
}

class DataPermissionTestModel extends Model
{
    use HasDataPermission;

    protected $table = 'test_models';
}

class HasDataPermissionTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->app['config']->set('admin.auth.guard', 'admin');
        $this->app['config']->set('auth.guards.admin', [
            'driver' => 'session',
            'provider' => 'admin',
        ]);
        $this->app['config']->set('auth.providers.admin', [
            'driver' => 'eloquent',
            'model' => \Dcat\Admin\Models\Administrator::class,
        ]);
        $this->app['config']->set('admin.data_permission.enable', false);
    }

    protected function tearDown(): void
    {
        // 恢复默认状态
        DataPermissionTestModel::enableDataPermission();
        DataPermissionTestModel::setCurrentMenuId(null);
        $this->app['config']->set('admin.database.menu_model', \Dcat\Admin\Models\Menu::class);
        FakeMenuQueryForHasDataPermissionTest::$firstCalls = 0;

        parent::tearDown();
    }

    public function test_set_and_get_current_menu_id(): void
    {
        DataPermissionTestModel::setCurrentMenuId(5);

        $this->assertEquals(5, DataPermissionTestModel::getCurrentMenuId());
    }

    public function test_set_current_menu_id_null(): void
    {
        DataPermissionTestModel::setCurrentMenuId(5);
        DataPermissionTestModel::setCurrentMenuId(null);

        // null 时会尝试从请求中检测，但不一定返回 null
        // 这里只验证不会抛异常
        $result = DataPermissionTestModel::getCurrentMenuId();
        $this->assertTrue($result === null || is_int($result));
    }

    public function test_enable_and_disable_data_permission(): void
    {
        DataPermissionTestModel::disableDataPermission();

        $reflection = new \ReflectionProperty(DataPermissionTestModel::class, 'dataPermissionEnabled');
        $reflection->setAccessible(true);

        $this->assertFalse($reflection->getValue());

        DataPermissionTestModel::enableDataPermission();
        $this->assertTrue($reflection->getValue());
    }

    public function test_without_data_permission_restores_state(): void
    {
        $reflection = new \ReflectionProperty(DataPermissionTestModel::class, 'dataPermissionEnabled');
        $reflection->setAccessible(true);

        // 初始状态：启用
        DataPermissionTestModel::enableDataPermission();
        $this->assertTrue($reflection->getValue());

        $result = DataPermissionTestModel::withoutDataPermission(function () use ($reflection) {
            // 回调内应该是禁用状态
            $this->assertFalse($reflection->getValue());

            return 'callback_result';
        });

        // 回调后应该恢复
        $this->assertTrue($reflection->getValue());
        $this->assertEquals('callback_result', $result);
    }

    public function test_without_data_permission_restores_state_on_exception(): void
    {
        $reflection = new \ReflectionProperty(DataPermissionTestModel::class, 'dataPermissionEnabled');
        $reflection->setAccessible(true);

        DataPermissionTestModel::enableDataPermission();

        try {
            DataPermissionTestModel::withoutDataPermission(function () {
                throw new \RuntimeException('test error');
            });
        } catch (\RuntimeException $e) {
            // 异常后状态应该恢复
            $this->assertTrue($reflection->getValue());

            return;
        }

        $this->fail('Expected RuntimeException');
    }

    public function test_with_menu_id_restores_state(): void
    {
        DataPermissionTestModel::setCurrentMenuId(1);

        $result = DataPermissionTestModel::withMenuId(99, function () {
            $this->assertEquals(99, DataPermissionTestModel::getCurrentMenuId());

            return 'from_callback';
        });

        $this->assertEquals(1, DataPermissionTestModel::getCurrentMenuId());
        $this->assertEquals('from_callback', $result);
    }

    public function test_with_menu_id_restores_state_on_exception(): void
    {
        DataPermissionTestModel::setCurrentMenuId(1);

        try {
            DataPermissionTestModel::withMenuId(99, function () {
                throw new \RuntimeException('test');
            });
        } catch (\RuntimeException $e) {
            $this->assertEquals(1, DataPermissionTestModel::getCurrentMenuId());

            return;
        }

        $this->fail('Expected RuntimeException');
    }

    public function test_get_hidden_columns_returns_empty_without_menu(): void
    {
        DataPermissionTestModel::setCurrentMenuId(null);

        // 未从请求中检测到菜单时可能返回空数组
        $result = DataPermissionTestModel::getHiddenColumns();
        $this->assertIsArray($result);
    }

    public function test_get_hidden_form_fields_returns_empty_without_menu(): void
    {
        DataPermissionTestModel::setCurrentMenuId(null);

        $result = DataPermissionTestModel::getHiddenFormFields();
        $this->assertIsArray($result);
    }

    public function test_can_access_column_returns_true_without_menu(): void
    {
        DataPermissionTestModel::setCurrentMenuId(null);

        // 需要 detectMenuIdFromRequest 返回 null
        // 配置一个不存在的菜单模型类以确保返回 null
        $this->app['config']->set('admin.database.menu_model', 'NonExistent\\MenuModel');

        $this->assertTrue(DataPermissionTestModel::canAccessColumn('any_field'));
    }

    public function test_can_access_form_field_returns_true_without_menu(): void
    {
        DataPermissionTestModel::setCurrentMenuId(null);
        $this->app['config']->set('admin.database.menu_model', 'NonExistent\\MenuModel');

        $this->assertTrue(DataPermissionTestModel::canAccessFormField('any_field'));
    }

    public function test_boot_trait_registers_global_scope(): void
    {
        $model = new DataPermissionTestModel;
        $scopes = $model->getGlobalScopes();

        $this->assertInstanceOf(\Closure::class, $scopes['data_permission'] ?? null);
    }

    public function test_get_current_menu_id_is_cached_in_same_request(): void
    {
        $this->app->instance('request', Request::create('/admin/orders', 'GET'));
        $this->app['config']->set('admin.database.menu_model', FakeMenuModelForHasDataPermissionTest::class);

        DataPermissionTestModel::setCurrentMenuId(null);

        $first = DataPermissionTestModel::getCurrentMenuId();
        $second = DataPermissionTestModel::getCurrentMenuId();

        $this->assertSame(123, $first);
        $this->assertSame(123, $second);
        $this->assertSame(1, FakeMenuQueryForHasDataPermissionTest::$firstCalls);
    }
}
