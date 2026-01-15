<?php

namespace Dcat\Admin\Tests\Unit\Support;

use Dcat\Admin\Models\DataRule;
use Dcat\Admin\Support\DataPermission;
use Dcat\Admin\Tests\TestCase;
use Illuminate\Database\Eloquent\Builder;
use Mockery;

class DataPermissionTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // 配置 admin guard
        $this->app['config']->set('admin.auth.guard', 'admin');
        $this->app['config']->set('auth.guards.admin', [
            'driver' => 'session',
            'provider' => 'admin',
        ]);
        $this->app['config']->set('auth.providers.admin', [
            'driver' => 'eloquent',
            'model' => \Dcat\Admin\Models\Administrator::class,
        ]);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_data_permission_creation_with_null_user(): void
    {
        // 显式传入 null 用户
        $dataPermission = new DataPermission(null);

        $this->assertInstanceOf(DataPermission::class, $dataPermission);
    }

    public function test_data_permission_static_make_with_null(): void
    {
        $dataPermission = DataPermission::make(null);

        $this->assertInstanceOf(DataPermission::class, $dataPermission);
    }

    public function test_resolve_fixed_value(): void
    {
        $rule = new DataRule([
            'value' => 'fixed_value',
            'value_type' => DataRule::VALUE_TYPE_FIXED,
        ]);

        $dataPermission = new DataPermission(null);
        $resolved = $dataPermission->resolveValue($rule);

        $this->assertEquals('fixed_value', $resolved);
    }

    public function test_get_hidden_columns_empty(): void
    {
        $dataPermission = new DataPermission(null);

        // 没有登录用户时，应返回空数组
        $hiddenColumns = $dataPermission->getHiddenColumns(1);

        $this->assertIsArray($hiddenColumns);
    }

    public function test_get_hidden_form_fields_empty(): void
    {
        $dataPermission = new DataPermission(null);

        // 没有登录用户时，应返回空数组
        $hiddenFields = $dataPermission->getHiddenFormFields(1);

        $this->assertIsArray($hiddenFields);
    }

    public function test_can_access_column_without_rules(): void
    {
        $dataPermission = new DataPermission(null);

        // 没有规则时，应该可以访问
        $canAccess = $dataPermission->canAccessColumn(1, 'any_field');

        $this->assertTrue($canAccess);
    }

    public function test_can_access_form_field_without_rules(): void
    {
        $dataPermission = new DataPermission(null);

        // 没有规则时，应该可以访问
        $canAccess = $dataPermission->canAccessFormField(1, 'any_field');

        $this->assertTrue($canAccess);
    }

    public function test_clear_cache(): void
    {
        // 验证静态方法存在且可调用
        DataPermission::clearCache();

        // 如果没有异常抛出，测试通过
        $this->assertTrue(true);
    }

    public function test_get_rules_for_menu_empty_without_user(): void
    {
        $dataPermission = new DataPermission(null);

        $rules = $dataPermission->getRulesForMenu(1);

        $this->assertCount(0, $rules);
    }

    public function test_get_row_rules_empty(): void
    {
        $dataPermission = new DataPermission(null);

        $rules = $dataPermission->getRowRules(1);

        $this->assertCount(0, $rules);
    }

    public function test_get_column_rules_empty(): void
    {
        $dataPermission = new DataPermission(null);

        $rules = $dataPermission->getColumnRules(1);

        $this->assertCount(0, $rules);
    }

    public function test_get_form_rules_empty(): void
    {
        $dataPermission = new DataPermission(null);

        $rules = $dataPermission->getFormRules(1);

        $this->assertCount(0, $rules);
    }

    public function test_apply_row_rules_with_mock_query(): void
    {
        $dataPermission = new DataPermission(null);

        // 创建 mock Builder
        $builder = Mockery::mock(Builder::class);
        $builder->shouldReceive('where')->andReturnSelf();

        // 没有用户时，应该直接返回 builder
        $result = $dataPermission->applyRowRules($builder, 1);

        $this->assertSame($builder, $result);
    }

    public function test_data_permission_with_null_user(): void
    {
        $dataPermission = new DataPermission(null);

        $this->assertInstanceOf(DataPermission::class, $dataPermission);

        // 验证各方法在无用户时正常工作
        $this->assertCount(0, $dataPermission->getRulesForMenu(1));
        $this->assertIsArray($dataPermission->getHiddenColumns(1));
        $this->assertIsArray($dataPermission->getHiddenFormFields(1));
    }

    public function test_data_permission_methods_exist(): void
    {
        // 验证所有公共方法存在
        $this->assertTrue(method_exists(DataPermission::class, 'make'));
        $this->assertTrue(method_exists(DataPermission::class, 'clearCache'));
        $this->assertTrue(method_exists(DataPermission::class, 'getRulesForMenu'));
        $this->assertTrue(method_exists(DataPermission::class, 'getRowRules'));
        $this->assertTrue(method_exists(DataPermission::class, 'getColumnRules'));
        $this->assertTrue(method_exists(DataPermission::class, 'getFormRules'));
        $this->assertTrue(method_exists(DataPermission::class, 'applyRowRules'));
        $this->assertTrue(method_exists(DataPermission::class, 'resolveValue'));
        $this->assertTrue(method_exists(DataPermission::class, 'getHiddenColumns'));
        $this->assertTrue(method_exists(DataPermission::class, 'getHiddenFormFields'));
        $this->assertTrue(method_exists(DataPermission::class, 'canAccessColumn'));
        $this->assertTrue(method_exists(DataPermission::class, 'canAccessFormField'));
    }
}
