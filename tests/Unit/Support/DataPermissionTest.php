<?php

namespace Dcat\Admin\Tests\Unit\Support;

use Dcat\Admin\Models\DataRule;
use Dcat\Admin\Support\DataPermission;
use Dcat\Admin\Tests\TestCase;
use Illuminate\Database\Eloquent\Builder;
use Mockery;

class FakeDepartmentRelationForDataPermissionTest
{
    public int $calls = 0;

    public function __construct(private $department) {}

    public function first()
    {
        $this->calls++;

        return $this->department;
    }
}

class FakeUserForDataPermissionTest
{
    public int $id = 1;

    public string $username = 'tester';

    public function __construct(private FakeDepartmentRelationForDataPermissionTest $relation) {}

    public function primaryDepartment(): FakeDepartmentRelationForDataPermissionTest
    {
        return $this->relation;
    }
}

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
        $dataPermission = DataPermission::make(null);
        $this->assertInstanceOf(DataPermission::class, $dataPermission);

        DataPermission::clearCache();
        DataPermission::clearCache();

        $this->assertCount(0, $dataPermission->getRulesForMenu(1));
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

    public function test_primary_department_query_is_cached_in_single_resolve_cycle(): void
    {
        $this->app['config']->set('admin.department.enable', true);

        $relation = new FakeDepartmentRelationForDataPermissionTest((object) [
            'id' => 9,
            'path' => '1/9',
        ]);
        $user = new FakeUserForDataPermissionTest($relation);
        $rule = new DataRule([
            'value' => '{department_id}-{department_path}',
            'value_type' => DataRule::VALUE_TYPE_VARIABLE,
        ]);

        $dataPermission = new DataPermission($user);
        $resolved = $dataPermission->resolveValue($rule);

        $this->assertSame('9-1/9', $resolved);
        $this->assertSame(1, $relation->calls);
    }
}
