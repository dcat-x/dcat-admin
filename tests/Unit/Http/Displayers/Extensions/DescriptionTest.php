<?php

namespace Dcat\Admin\Tests\Unit\Http\Displayers\Extensions;

use Dcat\Admin\Grid\Displayers\AbstractDisplayer;
use Dcat\Admin\Http\Displayers\Extensions\Description;
use Dcat\Admin\Tests\TestCase;

class DescriptionTest extends TestCase
{
    public function test_extends_abstract_displayer(): void
    {
        $this->assertTrue(is_subclass_of(Description::class, AbstractDisplayer::class));
    }

    public function test_class_exists(): void
    {
        $this->assertTrue(class_exists(Description::class));
    }

    public function test_display_method_exists(): void
    {
        $this->assertTrue(method_exists(Description::class, 'display'));
    }

    public function test_class_has_expected_methods(): void
    {
        $reflection = new \ReflectionClass(Description::class);

        $this->assertTrue($reflection->hasMethod('display'));
        $this->assertTrue($reflection->hasMethod('resolveSettingForm'));
        $this->assertTrue($reflection->hasMethod('resolveAction'));
    }
}
