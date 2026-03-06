<?php

namespace Dcat\Admin\Tests\Unit\Console;

use Dcat\Admin\Console\GeneratorCommand;
use Dcat\Admin\Tests\TestCase;
use Illuminate\Console\Command;
use Mockery;

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

    public function test_has_files_property(): void
    {
        $this->assertTrue(property_exists(GeneratorCommand::class, 'files'));

        $ref = new \ReflectionProperty(GeneratorCommand::class, 'files');

        $this->assertTrue($ref->isProtected());
    }

    public function test_has_type_property(): void
    {
        $this->assertTrue(property_exists(GeneratorCommand::class, 'type'));

        $ref = new \ReflectionProperty(GeneratorCommand::class, 'type');

        $this->assertTrue($ref->isProtected());
    }

    public function test_has_base_directory_property(): void
    {
        $this->assertTrue(property_exists(GeneratorCommand::class, 'baseDirectory'));

        $ref = new \ReflectionProperty(GeneratorCommand::class, 'baseDirectory');

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

    public function test_reserved_names_contains_specific_php_reserved_words(): void
    {
        $ref = new \ReflectionProperty(GeneratorCommand::class, 'reservedNames');
        $reservedNames = $ref->getDefaultValue();

        $expectedWords = [
            'abstract',
            'class',
            'function',
            'return',
            'yield',
            'if',
            'else',
            'while',
            'for',
            'switch',
            'try',
            'catch',
            'finally',
            'throw',
            'new',
            'extends',
            'implements',
            'interface',
            'trait',
            'public',
            'protected',
            'private',
            'static',
        ];

        foreach ($expectedWords as $word) {
            $this->assertContains($word, $reservedNames, "Reserved names should contain '{$word}'");
        }
    }

    public function test_has_required_methods(): void
    {
        $methods = [
            'handle',
            'qualifyClass',
            'qualifyModel',
            'getDefaultNamespace',
            'alreadyExists',
            'makeDirectory',
            'buildClass',
            'replaceNamespace',
            'getNamespace',
            'replaceClass',
            'sortImports',
            'getNameInput',
            'isReservedName',
            'viewPath',
            'getArguments',
            'rootNamespace',
            'getPath',
            'getBaseDir',
            'askBaseDirectory',
            'userProviderModel',
        ];

        foreach ($methods as $method) {
            $this->assertTrue(
                method_exists(GeneratorCommand::class, $method),
                "GeneratorCommand should have method '{$method}'"
            );
        }
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
}
