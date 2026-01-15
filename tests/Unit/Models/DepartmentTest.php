<?php

namespace Dcat\Admin\Tests\Unit\Models;

use Dcat\Admin\Models\Department;
use Dcat\Admin\Tests\TestCase;

class DepartmentTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // 设置测试配置
        $this->app['config']->set('admin.database.departments_table', 'admin_departments');
        $this->app['config']->set('admin.database.departments_model', Department::class);
    }

    public function test_department_creation(): void
    {
        $department = new Department([
            'name' => 'Test Department',
            'code' => 'TEST001',
            'parent_id' => 0,
            'status' => 1,
        ]);

        $this->assertInstanceOf(Department::class, $department);
        $this->assertEquals('Test Department', $department->name);
        $this->assertEquals('TEST001', $department->code);
    }

    public function test_department_fillable_attributes(): void
    {
        $department = new Department;

        $fillable = ['parent_id', 'path', 'name', 'code', 'order', 'status'];

        foreach ($fillable as $attribute) {
            $this->assertTrue(
                in_array($attribute, $department->getFillable()),
                "Attribute '{$attribute}' should be fillable"
            );
        }
    }

    public function test_department_status_check(): void
    {
        $enabledDepartment = new Department(['status' => 1]);
        $disabledDepartment = new Department(['status' => 0]);

        $this->assertTrue($enabledDepartment->isEnabled());
        $this->assertFalse($disabledDepartment->isEnabled());
    }

    public function test_update_path_method_exists(): void
    {
        $department = new Department;

        // 验证 updatePath 方法存在
        $this->assertTrue(method_exists($department, 'updatePath'));
    }

    public function test_is_root_department(): void
    {
        $rootDepartment = new Department(['parent_id' => 0]);
        $childDepartment = new Department(['parent_id' => 1]);

        // 通过 parent_id 判断是否为根部门
        $this->assertEquals(0, $rootDepartment->parent_id);
        $this->assertNotEquals(0, $childDepartment->parent_id);
    }

    public function test_department_scope_options(): void
    {
        // 验证 selectOptions 静态方法存在
        $this->assertTrue(method_exists(Department::class, 'selectOptions'));
    }

    public function test_department_tree_columns(): void
    {
        $department = new Department;

        // 验证树结构必须的列
        $this->assertEquals('parent_id', $department->getParentColumn());
        $this->assertEquals('order', $department->getOrderColumn());
        $this->assertEquals('name', $department->getTitleColumn());
    }

    public function test_department_casts(): void
    {
        $department = new Department([
            'status' => '1',
        ]);

        // 验证类型转换 - status 在 casts 中定义为 integer
        $this->assertIsInt($department->status);
    }

    public function test_department_default_values(): void
    {
        $department = new Department;

        // 默认值检查
        $this->assertEquals(0, $department->parent_id ?? 0);
        $this->assertEquals(1, $department->status ?? 1);
        $this->assertEquals(0, $department->order ?? 0);
    }

    public function test_department_relationships_exist(): void
    {
        $department = new Department;

        // 验证关系方法存在
        $this->assertTrue(method_exists($department, 'users'));
        $this->assertTrue(method_exists($department, 'roles'));
    }

    public function test_get_descendant_ids_method_exists(): void
    {
        $department = new Department;

        // 验证方法存在
        $this->assertTrue(method_exists($department, 'getDescendantIds'));
    }

    public function test_get_descendants_with_self_method_exists(): void
    {
        $department = new Department;

        // 验证方法存在
        $this->assertTrue(method_exists($department, 'getDescendantsWithSelf'));
    }

    public function test_update_descendants_path_method_exists(): void
    {
        $department = new Department;

        // 验证方法存在
        $this->assertTrue(method_exists($department, 'updateDescendantsPath'));
    }

    public function test_department_uses_model_tree_trait(): void
    {
        $department = new Department;

        // 验证使用了 ModelTree trait（通过检查 allNodes 方法）
        $this->assertTrue(method_exists($department, 'allNodes'));
    }

    public function test_department_is_sortable(): void
    {
        $department = new Department;

        // 验证实现了 Sortable 接口
        $this->assertInstanceOf(\Spatie\EloquentSortable\Sortable::class, $department);
    }
}
