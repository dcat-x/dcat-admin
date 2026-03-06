<?php

namespace Dcat\Admin\Tests\Unit\Console;

use Dcat\Admin\Console\FormCommand;
use Dcat\Admin\Console\GeneratorCommand;
use Dcat\Admin\Tests\TestCase;
use Mockery;
use PHPUnit\Framework\Attributes\DataProvider;

class FormCommandTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }

    public function test_reflection_can_load_class_metadata(): void
    {
        $ref = new \ReflectionClass(FormCommand::class);

        $this->assertSame(FormCommand::class, $ref->getName());
    }

    public function test_extends_generator_command(): void
    {
        $parents = class_parents(FormCommand::class);

        $this->assertContains(GeneratorCommand::class, $parents);
    }

    public function test_signature_contains_admin_form(): void
    {
        $ref = new \ReflectionProperty(FormCommand::class, 'signature');
        $defaultValue = $ref->getDefaultValue();

        $this->assertStringContainsString('admin:form', $defaultValue);
    }

    public function test_signature_contains_name_argument(): void
    {
        $ref = new \ReflectionProperty(FormCommand::class, 'signature');
        $defaultValue = $ref->getDefaultValue();

        $this->assertStringContainsString('{name}', $defaultValue);
    }

    public function test_description_default_value(): void
    {
        $ref = new \ReflectionProperty(FormCommand::class, 'description');
        $defaultValue = $ref->getDefaultValue();

        $this->assertSame('Make admin form widget', $defaultValue);
    }

    #[DataProvider('requiredMethodProvider')]
    public function test_has_required_methods(string $method): void
    {
        $reflection = new \ReflectionMethod(FormCommand::class, $method);

        $this->assertSame($method, $reflection->getName());
    }

    public function test_get_stub_is_protected(): void
    {
        $ref = new \ReflectionMethod(FormCommand::class, 'getStub');

        $this->assertTrue($ref->isProtected());
    }

    public function test_handle_is_public(): void
    {
        $ref = new \ReflectionMethod(FormCommand::class, 'handle');

        $this->assertTrue($ref->isPublic());
    }

    public static function requiredMethodProvider(): array
    {
        return [
            ['handle'],
            ['getStub'],
            ['getDefaultNamespace'],
            ['getNameInput'],
        ];
    }
}
