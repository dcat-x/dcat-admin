<?php

namespace Dcat\Admin\Tests\Unit\Grid\Filter\Presenter;

use Dcat\Admin\Grid\Filter\Presenter\MultipleSelectTable;
use Dcat\Admin\Grid\Filter\Presenter\SelectTable;
use Dcat\Admin\Tests\TestCase;
use Mockery;

class MultipleSelectTableTest extends TestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }

    public function test_extends_select_table(): void
    {
        $this->assertTrue(is_subclass_of(MultipleSelectTable::class, SelectTable::class));
    }

    public function test_has_css_property(): void
    {
        $ref = new \ReflectionProperty(MultipleSelectTable::class, 'css');
        $ref->setAccessible(true);
        $this->assertContains('@select2', $ref->getDefaultValue());
    }

    public function test_view_property(): void
    {
        $ref = new \ReflectionProperty(MultipleSelectTable::class, 'view');
        $ref->setAccessible(true);
        $this->assertSame('admin::filter.selecttable', $ref->getDefaultValue());
    }

    public function test_max_default_is_zero(): void
    {
        $ref = new \ReflectionProperty(MultipleSelectTable::class, 'max');
        $ref->setAccessible(true);
        $this->assertSame(0, $ref->getDefaultValue());
    }

    public function test_max_method_exists(): void
    {
        $this->assertTrue(method_exists(MultipleSelectTable::class, 'max'));
    }

    public function test_add_script_is_protected(): void
    {
        $ref = new \ReflectionMethod(MultipleSelectTable::class, 'addScript');
        $this->assertTrue($ref->isProtected());
    }
}
