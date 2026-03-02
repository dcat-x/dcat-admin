<?php

namespace Dcat\Admin\Tests\Unit\Http\Actions\Extensions;

use Dcat\Admin\Grid\Tools\AbstractTool;
use Dcat\Admin\Http\Actions\Extensions\InstallFromLocal;
use Dcat\Admin\Tests\TestCase;
use Mockery;

class InstallFromLocalTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_class_exists(): void
    {
        $this->assertTrue(class_exists(InstallFromLocal::class));
    }

    public function test_is_subclass_of_abstract_tool(): void
    {
        $this->assertTrue(is_subclass_of(InstallFromLocal::class, AbstractTool::class));
    }

    public function test_style_property_default_value(): void
    {
        $ref = new \ReflectionProperty(InstallFromLocal::class, 'style');
        $ref->setAccessible(true);

        $instance = (new \ReflectionClass(InstallFromLocal::class))->newInstanceWithoutConstructor();
        $this->assertSame('btn btn-primary', $ref->getValue($instance));
    }

    public function test_html_method_exists(): void
    {
        $this->assertTrue(method_exists(InstallFromLocal::class, 'html'));
    }
}
