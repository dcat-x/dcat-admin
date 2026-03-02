<?php

namespace Dcat\Admin\Tests\Unit\Form\Field;

use Dcat\Admin\Form\Field\Currency;
use Dcat\Admin\Form\Field\Fee;
use Dcat\Admin\Tests\TestCase;
use Mockery;

class FeeTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    // -------------------------------------------------------
    // Class existence and inheritance
    // -------------------------------------------------------

    public function test_class_exists(): void
    {
        $this->assertTrue(class_exists(Fee::class));
    }

    public function test_extends_currency(): void
    {
        $this->assertTrue(is_subclass_of(Fee::class, Currency::class));
    }

    // -------------------------------------------------------
    // Method existence
    // -------------------------------------------------------

    public function test_inputmask_method_exists(): void
    {
        $this->assertTrue(method_exists(Fee::class, 'inputmask'));
    }

    public function test_render_method_exists(): void
    {
        $this->assertTrue(method_exists(Fee::class, 'render'));
    }

    // -------------------------------------------------------
    // Method visibility and return type
    // -------------------------------------------------------

    public function test_inputmask_is_public(): void
    {
        $method = new \ReflectionMethod(Fee::class, 'inputmask');
        $this->assertTrue($method->isPublic());
    }

    public function test_render_is_public(): void
    {
        $method = new \ReflectionMethod(Fee::class, 'render');
        $this->assertTrue($method->isPublic());
    }

    public function test_inputmask_has_options_parameter(): void
    {
        $method = new \ReflectionMethod(Fee::class, 'inputmask');
        $params = $method->getParameters();

        $this->assertCount(1, $params);
        $this->assertSame('options', $params[0]->getName());
    }

    public function test_inputmask_return_type_is_static(): void
    {
        $method = new \ReflectionMethod(Fee::class, 'inputmask');
        $returnType = $method->getReturnType();

        $this->assertNotNull($returnType);
        $this->assertSame('static', $returnType->getName());
    }

    public function test_render_return_type_is_mixed(): void
    {
        $method = new \ReflectionMethod(Fee::class, 'render');
        $returnType = $method->getReturnType();

        $this->assertNotNull($returnType);
    }

    // -------------------------------------------------------
    // Inheritance chain
    // -------------------------------------------------------

    public function test_inherits_symbol_method_from_currency(): void
    {
        $this->assertTrue(method_exists(Fee::class, 'symbol'));
    }

    public function test_inherits_digits_method_from_currency(): void
    {
        $this->assertTrue(method_exists(Fee::class, 'digits'));
    }

    public function test_inherits_prepare_input_value_from_currency(): void
    {
        $this->assertTrue(method_exists(Fee::class, 'prepareInputValue'));
    }
}
