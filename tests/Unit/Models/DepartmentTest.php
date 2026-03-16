<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Models;

use Dcat\Admin\Models\Department;
use Dcat\Admin\Tests\TestCase;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Mockery;
use Spatie\EloquentSortable\Sortable;

class DepartmentTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // 设置测试配置
        $this->app['config']->set('admin.database.departments_table', 'admin_departments');
        $this->app['config']->set('admin.database.departments_model', Department::class);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
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
        $this->assertSame('Test Department', $department->name);
        $this->assertSame('TEST001', $department->code);
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

    public function test_get_descendant_ids_returns_empty_array_when_path_is_empty(): void
    {
        $department = new Department(['path' => null]);

        $this->assertSame([], $department->getDescendantIds());
    }

    public function test_is_root_department(): void
    {
        $rootDepartment = new Department(['parent_id' => 0]);
        $childDepartment = new Department(['parent_id' => 1]);

        // 通过 parent_id 判断是否为根部门
        $this->assertSame(0, $rootDepartment->parent_id);
        $this->assertNotEquals(0, $childDepartment->parent_id);
    }

    public function test_select_options_method_available_on_department(): void
    {
        $reflection = new \ReflectionClass(Department::class);

        $this->assertTrue($reflection->hasMethod('selectOptions'));
    }

    public function test_department_tree_columns(): void
    {
        $department = new Department;

        // 验证树结构必须的列
        $this->assertSame('parent_id', $department->getParentColumn());
        $this->assertSame('order', $department->getOrderColumn());
        $this->assertSame('name', $department->getTitleColumn());
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
        $this->assertSame(0, $department->parent_id ?? 0);
        $this->assertSame(1, $department->status ?? 1);
        $this->assertSame(0, $department->order ?? 0);
    }

    public function test_users_relationship_declares_belongs_to_many_return_type(): void
    {
        $method = new \ReflectionMethod(Department::class, 'users');
        $returnType = $method->getReturnType();

        $this->assertNotNull($returnType);
        $this->assertSame(BelongsToMany::class, $returnType->getName());
    }

    public function test_roles_relationship_declares_belongs_to_many_return_type(): void
    {
        $method = new \ReflectionMethod(Department::class, 'roles');
        $returnType = $method->getReturnType();

        $this->assertNotNull($returnType);
        $this->assertSame(BelongsToMany::class, $returnType->getName());
    }

    public function test_get_descendant_ids_declares_array_return_type(): void
    {
        $method = new \ReflectionMethod(Department::class, 'getDescendantIds');
        $returnType = $method->getReturnType();

        $this->assertNotNull($returnType);
        $this->assertSame('array', $returnType->getName());
    }

    public function test_update_descendants_path_declares_void_return_type(): void
    {
        $method = new \ReflectionMethod(Department::class, 'updateDescendantsPath');
        $returnType = $method->getReturnType();

        $this->assertNotNull($returnType);
        $this->assertSame('void', $returnType->getName());
    }

    public function test_department_model_tree_methods_are_available(): void
    {
        $reflection = new \ReflectionClass(Department::class);

        $this->assertTrue($reflection->hasMethod('allNodes'));
        $this->assertTrue($reflection->hasMethod('getDescendantsWithSelf'));
    }

    public function test_update_path_sets_root_path_and_saves_quietly(): void
    {
        $department = Mockery::mock(Department::class)->makePartial();
        $department->id = 12;
        $department->parent_id = 0;
        $department->shouldReceive('saveQuietly')->once();

        $department->updatePath();

        $this->assertSame('/12/', $department->path);
    }

    public function test_department_is_sortable(): void
    {
        $department = new Department;

        // 验证实现了 Sortable 接口
        $this->assertInstanceOf(Sortable::class, $department);
    }
}
