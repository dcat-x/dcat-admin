<?php

namespace Dcat\Admin\Tests\Unit\Extend;

use Dcat\Admin\Contracts\LazyRenderable;
use Dcat\Admin\Extend\Setting;
use Dcat\Admin\Tests\TestCase;
use Dcat\Admin\Traits\LazyWidget;
use Dcat\Admin\Widgets\Form;
use Mockery;

class SettingTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_class_exists(): void
    {
        $this->assertTrue(class_exists(Setting::class));
    }

    public function test_extends_form(): void
    {
        $this->assertTrue(is_subclass_of(Setting::class, Form::class));
    }

    public function test_implements_lazy_renderable(): void
    {
        $interfaces = class_implements(Setting::class);

        $this->assertArrayHasKey(LazyRenderable::class, $interfaces);
    }

    public function test_uses_lazy_widget_trait(): void
    {
        $traits = class_uses(Setting::class);

        $this->assertArrayHasKey(LazyWidget::class, $traits);
    }

    public function test_has_form_method(): void
    {
        $this->assertTrue(method_exists(Setting::class, 'form'));
    }

    public function test_has_title_method(): void
    {
        $this->assertTrue(method_exists(Setting::class, 'title'));
    }

    public function test_has_extension_method(): void
    {
        $this->assertTrue(method_exists(Setting::class, 'extension'));
    }
}
