<?php

namespace Dcat\Admin\Tests\Unit\Grid\Concerns;

use Dcat\Admin\Grid;
use Dcat\Admin\Grid\Concerns\HasDataPermission;
use Dcat\Admin\Tests\TestCase;
use Mockery;

class HasDataPermissionTest extends TestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }

    protected function makeHelper(): HasDataPermissionTestHelper
    {
        return new HasDataPermissionTestHelper;
    }

    public function test_data_permission_menu_id_initially_null(): void
    {
        $helper = $this->makeHelper();
        $ref = new \ReflectionProperty($helper, 'dataPermissionMenuId');
        $ref->setAccessible(true);
        $this->assertNull($ref->getValue($helper));
    }

    public function test_set_data_permission_menu_id(): void
    {
        $helper = $this->makeHelper();
        $result = $helper->setDataPermissionMenuId(42);
        $this->assertSame($helper, $result);

        $ref = new \ReflectionProperty($helper, 'dataPermissionMenuId');
        $ref->setAccessible(true);
        $this->assertSame(42, $ref->getValue($helper));
    }

    public function test_set_data_permission_menu_id_null(): void
    {
        $helper = $this->makeHelper();
        $helper->setDataPermissionMenuId(42);
        $helper->setDataPermissionMenuId(null);

        $ref = new \ReflectionProperty($helper, 'dataPermissionMenuId');
        $ref->setAccessible(true);
        $this->assertNull($ref->getValue($helper));
    }

    public function test_get_data_permission_menu_id_returns_set_value(): void
    {
        $helper = $this->makeHelper();
        $helper->setDataPermissionMenuId(10);
        $this->assertSame(10, $helper->getDataPermissionMenuId());
    }

    public function test_with_data_permission_sets_menu_id(): void
    {
        $helper = $this->makeHelper();
        $result = $helper->withDataPermission(99);
        $this->assertSame($helper, $result);

        $ref = new \ReflectionProperty($helper, 'dataPermissionMenuId');
        $ref->setAccessible(true);
        $this->assertSame(99, $ref->getValue($helper));
    }

    public function test_with_data_permission_without_menu_id(): void
    {
        $helper = $this->makeHelper();
        $result = $helper->withDataPermission();
        $this->assertSame($helper, $result);
    }

    public function test_apply_column_permissions_returns_self_when_disabled(): void
    {
        config(['admin.data_permission.enable' => false]);
        $helper = $this->makeHelper();
        $result = $helper->applyColumnPermissions();
        $this->assertSame($helper, $result);
    }

    public function test_detect_menu_id_from_request_is_protected(): void
    {
        $ref = new \ReflectionMethod(HasDataPermissionTestHelper::class, 'detectMenuIdFromRequest');
        $this->assertTrue($ref->isProtected());
    }
}

class HasDataPermissionTestHelper extends Grid
{
    use HasDataPermission;

    public function __construct()
    {
        // Skip parent constructor
    }
}
