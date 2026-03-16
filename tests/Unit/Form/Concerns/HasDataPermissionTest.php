<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Form\Concerns;

use Dcat\Admin\Form\Concerns\HasDataPermission;
use Dcat\Admin\Tests\TestCase;
use Mockery;

class HasDataPermissionTest extends TestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }

    protected function makeHelper(): FormHasDataPermissionTestHelper
    {
        return new FormHasDataPermissionTestHelper;
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
        $result = $helper->setDataPermissionMenuId(5);
        $this->assertSame($helper, $result);

        $ref = new \ReflectionProperty($helper, 'dataPermissionMenuId');
        $ref->setAccessible(true);
        $this->assertSame(5, $ref->getValue($helper));
    }

    public function test_get_data_permission_menu_id_returns_set_value(): void
    {
        $helper = $this->makeHelper();
        $helper->setDataPermissionMenuId(10);
        $this->assertSame(10, $helper->getDataPermissionMenuId());
    }

    public function test_get_data_permission_menu_id_returns_null_when_not_set(): void
    {
        $helper = $this->makeHelper();
        // detectMenuIdFromRequest will likely return null in test env
        $result = $helper->getDataPermissionMenuId();
        $this->assertNull($result);
    }

    public function test_apply_form_field_permissions_returns_self_when_disabled(): void
    {
        config(['admin.data_permission.enable' => false]);
        $helper = $this->makeHelper();
        $result = $helper->applyFormFieldPermissions();
        $this->assertSame($helper, $result);
    }

    public function test_with_data_permission_sets_menu_id(): void
    {
        $helper = $this->makeHelper();
        $result = $helper->withDataPermission(77);
        $this->assertSame($helper, $result);

        $ref = new \ReflectionProperty($helper, 'dataPermissionMenuId');
        $ref->setAccessible(true);
        $this->assertSame(77, $ref->getValue($helper));
    }

    public function test_with_data_permission_without_menu_id(): void
    {
        $helper = $this->makeHelper();
        $result = $helper->withDataPermission();
        $this->assertSame($helper, $result);
    }

    public function test_detect_menu_id_from_request_is_protected(): void
    {
        $ref = new \ReflectionMethod(FormHasDataPermissionTestHelper::class, 'detectMenuIdFromRequest');
        $this->assertTrue($ref->isProtected());
    }

    public function test_apply_field_rule_is_protected(): void
    {
        $ref = new \ReflectionMethod(FormHasDataPermissionTestHelper::class, 'applyFieldRule');
        $this->assertTrue($ref->isProtected());
    }

    public function test_find_field_by_column_is_protected(): void
    {
        $ref = new \ReflectionMethod(FormHasDataPermissionTestHelper::class, 'findFieldByColumn');
        $this->assertTrue($ref->isProtected());
    }
}

class FormHasDataPermissionTestHelper
{
    use HasDataPermission;

    public function fields()
    {
        return collect([]);
    }

    public function built($callback)
    {
        // No-op in test
        return $this;
    }
}
