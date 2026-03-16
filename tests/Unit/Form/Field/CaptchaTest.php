<?php

declare(strict_types=1);

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

    public function test_captcha_extends_text_field(): void
    {
        $reflection = new \ReflectionClass(Captcha::class);
        $parent = $reflection->getParentClass();

        $this->assertSame(Text::class, $parent?->getName());
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

    public function test_set_form_calls_ignore_on_form_when_available(): void
    {
        $reflection = new \ReflectionClass(Captcha::class);
        /** @var Captcha $field */
        $field = $reflection->newInstanceWithoutConstructor();
        $column = new \ReflectionProperty($field, 'column');
        $column->setAccessible(true);
        $column->setValue($field, '__captcha__');

        $form = new class
        {
            public array $ignored = [];

            public function ignore(string $column): void
            {
                $this->ignored[] = $column;
            }
        };

        $result = $field->setForm($form);

        $this->assertSame($field, $result);
        $this->assertSame(['__captcha__'], $form->ignored);
    }

    public function test_render_method_signature_is_public_and_parameterless(): void
    {
        $method = new \ReflectionMethod(Captcha::class, 'render');

        $this->assertTrue($method->isPublic());
        $this->assertCount(0, $method->getParameters());
    }
}
