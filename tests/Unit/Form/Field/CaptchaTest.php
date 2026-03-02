<?php

namespace Dcat\Admin\Tests\Unit\Form\Field;

use Dcat\Admin\Form\Field\Captcha;
use Dcat\Admin\Form\Field\Text;
use Dcat\Admin\Tests\TestCase;
use Mockery;

class CaptchaTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    // -------------------------------------------------------
    // Class structure
    // -------------------------------------------------------

    public function test_class_exists(): void
    {
        $this->assertTrue(class_exists(Captcha::class));
    }

    public function test_is_subclass_of_text(): void
    {
        $this->assertTrue(is_subclass_of(Captcha::class, Text::class));
    }

    // -------------------------------------------------------
    // Default property values via reflection
    // -------------------------------------------------------

    public function test_rules_default(): void
    {
        $ref = new \ReflectionProperty(Captcha::class, 'rules');
        $ref->setAccessible(true);

        $this->assertSame(['required', 'captcha'], $ref->getDefaultValue());
    }

    public function test_view_default(): void
    {
        $ref = new \ReflectionProperty(Captcha::class, 'view');
        $ref->setAccessible(true);

        $this->assertSame('admin::form.captcha', $ref->getDefaultValue());
    }

    // -------------------------------------------------------
    // Method existence
    // -------------------------------------------------------

    public function test_method_set_form_exists(): void
    {
        $this->assertTrue(method_exists(Captcha::class, 'setForm'));
    }

    public function test_method_render_exists(): void
    {
        $this->assertTrue(method_exists(Captcha::class, 'render'));
    }
}
