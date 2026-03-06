<?php

namespace Dcat\Admin\Tests\Unit\Form\Field;

use Dcat\Admin\Form\Field\HasDepends;
use Dcat\Admin\Tests\TestCase;
use Mockery;
use PHPUnit\Framework\Attributes\DataProvider;

class HasDependsTest extends TestCase
{
    protected function dependsMethod(): \ReflectionMethod
    {
        return new \ReflectionMethod(HasDepends::class, 'depends');
    }

    protected function dependsParameters(): array
    {
        return $this->dependsMethod()->getParameters();
    }

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
        $this->assertSame(2, $this->dependsMethod()->getNumberOfParameters());
    }

    // -------------------------------------------------------
    // Method visibility
    // -------------------------------------------------------

    public function test_depends_is_public(): void
    {
        $this->assertTrue($this->dependsMethod()->isPublic());
    }

    // -------------------------------------------------------
    // Method parameter checks
    // -------------------------------------------------------

    public function test_depends_has_expected_parameters(): void
    {
        $params = $this->dependsParameters();

        $this->assertCount(2, $params);
        $this->assertSame('fields', $params[0]->getName());
        $this->assertSame('clear', $params[1]->getName());
    }

    #[DataProvider('defaultValueProvider')]
    public function test_depends_parameter_defaults(int $index, mixed $defaultValue): void
    {
        $params = $this->dependsParameters();

        $this->assertTrue($params[$index]->isDefaultValueAvailable());
        $this->assertSame($defaultValue, $params[$index]->getDefaultValue());
    }

    public function test_depends_clear_has_bool_type(): void
    {
        $params = $this->dependsParameters();

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

    public static function defaultValueProvider(): array
    {
        return [
            'fields' => [0, []],
            'clear' => [1, true],
        ];
    }
}
