<?php

namespace Dcat\Admin\Tests\Unit\Form\Field;

use Dcat\Admin\Form\Field\HasDepends;
use Dcat\Admin\Tests\TestCase;
use Mockery;

class HasDependsTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    // -------------------------------------------------------
    // Trait existence
    // -------------------------------------------------------

    public function test_trait_exists(): void
    {
        $this->assertTrue(trait_exists(HasDepends::class));
    }

    // -------------------------------------------------------
    // Method existence
    // -------------------------------------------------------

    public function test_depends_method_signature(): void
    {
        $method = new \ReflectionMethod(HasDepends::class, 'depends');

        $this->assertSame(2, $method->getNumberOfParameters());
    }

    // -------------------------------------------------------
    // Method visibility
    // -------------------------------------------------------

    public function test_depends_is_public(): void
    {
        $method = new \ReflectionMethod(HasDepends::class, 'depends');
        $this->assertTrue($method->isPublic());
    }

    // -------------------------------------------------------
    // Method parameter checks
    // -------------------------------------------------------

    public function test_depends_has_expected_parameters(): void
    {
        $method = new \ReflectionMethod(HasDepends::class, 'depends');
        $params = $method->getParameters();

        $this->assertCount(2, $params);
        $this->assertSame('fields', $params[0]->getName());
        $this->assertSame('clear', $params[1]->getName());
    }

    public function test_depends_fields_default_is_empty_array(): void
    {
        $method = new \ReflectionMethod(HasDepends::class, 'depends');
        $params = $method->getParameters();

        $this->assertTrue($params[0]->isDefaultValueAvailable());
        $this->assertSame([], $params[0]->getDefaultValue());
    }

    public function test_depends_clear_default_is_true(): void
    {
        $method = new \ReflectionMethod(HasDepends::class, 'depends');
        $params = $method->getParameters();

        $this->assertTrue($params[1]->isDefaultValueAvailable());
        $this->assertTrue($params[1]->getDefaultValue());
    }

    public function test_depends_clear_has_bool_type(): void
    {
        $method = new \ReflectionMethod(HasDepends::class, 'depends');
        $params = $method->getParameters();

        $type = $params[1]->getType();
        $this->assertNotNull($type);
        $this->assertSame('bool', $type->getName());
    }

    // -------------------------------------------------------
    // Trait reflection
    // -------------------------------------------------------

    public function test_trait_has_only_depends_method(): void
    {
        $reflection = new \ReflectionClass(HasDepends::class);
        $methods = $reflection->getMethods();

        $methodNames = array_map(fn ($m) => $m->getName(), $methods);
        $this->assertContains('depends', $methodNames);
        $this->assertCount(1, $methodNames);
    }
}
