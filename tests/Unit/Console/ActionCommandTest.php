<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Console;

use Dcat\Admin\Console\ActionCommand;
use Dcat\Admin\Console\GeneratorCommand;
use Dcat\Admin\Tests\TestCase;
use Mockery;
use PHPUnit\Framework\Attributes\DataProvider;

class ActionCommandTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }

    public function test_reflection_can_load_class_metadata(): void
    {
        $ref = new \ReflectionClass(ActionCommand::class);

        $this->assertSame(ActionCommand::class, $ref->getName());
    }

    public function test_extends_generator_command(): void
    {
        $parents = class_parents(ActionCommand::class);

        $this->assertContains(GeneratorCommand::class, $parents);
    }

    public function test_signature_default_value(): void
    {
        $ref = new \ReflectionProperty(ActionCommand::class, 'signature');
        $defaultValue = $ref->getDefaultValue();

        $this->assertSame('admin:action', $defaultValue);
    }

    public function test_description_default_value(): void
    {
        $ref = new \ReflectionProperty(ActionCommand::class, 'description');
        $defaultValue = $ref->getDefaultValue();

        $this->assertSame('Make a admin action', $defaultValue);
    }

    #[DataProvider('protectedPropertyProvider')]
    public function test_has_protected_properties(string $property): void
    {
        $this->assertTrue(property_exists(ActionCommand::class, $property));

        $ref = new \ReflectionProperty(ActionCommand::class, $property);

        $this->assertTrue($ref->isProtected());
    }

    public function test_has_namespace_map_property(): void
    {
        $this->assertTrue(property_exists(ActionCommand::class, 'namespaceMap'));

        $ref = new \ReflectionProperty(ActionCommand::class, 'namespaceMap');

        $this->assertTrue($ref->isProtected());
        $this->assertTrue($ref->hasDefaultValue());

        $defaultValue = $ref->getDefaultValue();
        $this->assertIsArray($defaultValue);
    }

    #[DataProvider('namespaceMapKeyProvider')]
    public function test_namespace_map_has_expected_keys(string $key): void
    {
        $ref = new \ReflectionProperty(ActionCommand::class, 'namespaceMap');
        $defaultValue = $ref->getDefaultValue();

        $this->assertContains($key, array_keys($defaultValue), "namespaceMap should have key '{$key}'");
    }

    public function test_namespace_map_values(): void
    {
        $ref = new \ReflectionProperty(ActionCommand::class, 'namespaceMap');
        $map = $ref->getDefaultValue();

        $this->assertSame('Grid', $map['grid-batch']);
        $this->assertSame('Grid', $map['grid-row']);
        $this->assertSame('Grid', $map['grid-tool']);
        $this->assertSame('Form', $map['form-tool']);
        $this->assertSame('Show', $map['show-tool']);
        $this->assertSame('Tree', $map['tree-row']);
        $this->assertSame('Tree', $map['tree-tool']);
    }

    #[DataProvider('requiredMethodProvider')]
    public function test_has_required_methods(string $method): void
    {
        $reflection = new \ReflectionMethod(ActionCommand::class, $method);

        $this->assertSame($method, $reflection->getName());
    }

    public function test_action_typs_is_protected(): void
    {
        $ref = new \ReflectionMethod(ActionCommand::class, 'actionTyps');

        $this->assertTrue($ref->isProtected());
    }

    public function test_get_stub_is_public(): void
    {
        $ref = new \ReflectionMethod(ActionCommand::class, 'getStub');

        $this->assertTrue($ref->isPublic());
    }

    public static function protectedPropertyProvider(): array
    {
        return [
            ['choice'],
            ['className'],
            ['namespace'],
        ];
    }

    public static function namespaceMapKeyProvider(): array
    {
        return [
            ['grid-batch'],
            ['grid-row'],
            ['grid-tool'],
            ['form-tool'],
            ['show-tool'],
            ['tree-row'],
            ['tree-tool'],
        ];
    }

    public static function requiredMethodProvider(): array
    {
        return [
            ['handle'],
            ['actionTyps'],
            ['replaceClass'],
            ['getStub'],
            ['getDefaultNamespace'],
            ['getNameInput'],
        ];
    }
}
