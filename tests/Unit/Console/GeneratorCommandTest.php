<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Console;

use Dcat\Admin\Console\GeneratorCommand;
use Dcat\Admin\Tests\TestCase;
use Illuminate\Console\Command;
use Mockery;
use PHPUnit\Framework\Attributes\DataProvider;

class GeneratorCommandTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }

    public function test_reflection_can_load_class_metadata(): void
    {
        $ref = new \ReflectionClass(GeneratorCommand::class);

        $this->assertSame(GeneratorCommand::class, $ref->getName());
    }

    public function test_is_abstract_class(): void
    {
        $ref = new \ReflectionClass(GeneratorCommand::class);

        $this->assertTrue($ref->isAbstract());
    }

    public function test_extends_illuminate_console_command(): void
    {
        $parents = class_parents(GeneratorCommand::class);

        $this->assertContains(Command::class, $parents);
    }

    #[DataProvider('protectedPropertyProvider')]
    public function test_has_protected_properties(string $property): void
    {
        $this->assertTrue(property_exists(GeneratorCommand::class, $property));

        $ref = new \ReflectionProperty(GeneratorCommand::class, $property);

        $this->assertTrue($ref->isProtected());
    }

    public function test_has_reserved_names_property(): void
    {
        $this->assertTrue(property_exists(GeneratorCommand::class, 'reservedNames'));

        $ref = new \ReflectionProperty(GeneratorCommand::class, 'reservedNames');

        $this->assertTrue($ref->isProtected());

        $this->assertTrue($ref->hasDefaultValue());
        $defaultValue = $ref->getDefaultValue();
        $this->assertIsArray($defaultValue);
        $this->assertNotEmpty($defaultValue);
    }

    #[DataProvider('reservedNameProvider')]
    public function test_reserved_names_contains_specific_php_reserved_words(string $word): void
    {
        $ref = new \ReflectionProperty(GeneratorCommand::class, 'reservedNames');
        $reservedNames = $ref->getDefaultValue();

        $this->assertContains($word, $reservedNames, "Reserved names should contain '{$word}'");
    }

    #[DataProvider('requiredMethodProvider')]
    public function test_has_required_methods(string $method): void
    {
        $reflection = new \ReflectionMethod(GeneratorCommand::class, $method);

        $this->assertSame($method, $reflection->getName());
    }

    public function test_get_stub_is_abstract_method(): void
    {
        $ref = new \ReflectionMethod(GeneratorCommand::class, 'getStub');

        $this->assertTrue($ref->isAbstract());
    }

    public function test_handle_is_public(): void
    {
        $ref = new \ReflectionMethod(GeneratorCommand::class, 'handle');

        $this->assertTrue($ref->isPublic());
    }

    public function test_is_reserved_name_is_protected(): void
    {
        $ref = new \ReflectionMethod(GeneratorCommand::class, 'isReservedName');

        $this->assertTrue($ref->isProtected());
    }

    public function test_sort_imports_is_protected(): void
    {
        $ref = new \ReflectionMethod(GeneratorCommand::class, 'sortImports');

        $this->assertTrue($ref->isProtected());
    }

    public function test_get_stub_is_protected(): void
    {
        $ref = new \ReflectionMethod(GeneratorCommand::class, 'getStub');

        $this->assertTrue($ref->isProtected());
    }

    public function test_qualify_class_is_protected(): void
    {
        $ref = new \ReflectionMethod(GeneratorCommand::class, 'qualifyClass');

        $this->assertTrue($ref->isProtected());
    }

    public static function protectedPropertyProvider(): array
    {
        return [
            ['files'],
            ['type'],
            ['baseDirectory'],
        ];
    }

    public static function reservedNameProvider(): array
    {
        return [
            ['abstract'],
            ['class'],
            ['function'],
            ['return'],
            ['yield'],
            ['if'],
            ['else'],
            ['while'],
            ['for'],
            ['switch'],
            ['try'],
            ['catch'],
            ['finally'],
            ['throw'],
            ['new'],
            ['extends'],
            ['implements'],
            ['interface'],
            ['trait'],
            ['public'],
            ['protected'],
            ['private'],
            ['static'],
        ];
    }

    public static function requiredMethodProvider(): array
    {
        return [
            ['handle'],
            ['qualifyClass'],
            ['qualifyModel'],
            ['getDefaultNamespace'],
            ['alreadyExists'],
            ['makeDirectory'],
            ['buildClass'],
            ['replaceNamespace'],
            ['getNamespace'],
            ['replaceClass'],
            ['sortImports'],
            ['getNameInput'],
            ['isReservedName'],
            ['viewPath'],
            ['getArguments'],
            ['rootNamespace'],
            ['getPath'],
            ['getBaseDir'],
            ['askBaseDirectory'],
            ['userProviderModel'],
        ];
    }
}
