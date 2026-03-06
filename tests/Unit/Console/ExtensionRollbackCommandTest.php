<?php

namespace Dcat\Admin\Tests\Unit\Console;

use Dcat\Admin\Console\ExtensionRollbackCommand;
use Dcat\Admin\Tests\TestCase;
use Illuminate\Console\Command;
use Mockery;

class ExtensionRollbackCommandTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }

    public function test_reflection_can_load_class_metadata(): void
    {
        $ref = new \ReflectionClass(ExtensionRollbackCommand::class);

        $this->assertSame(ExtensionRollbackCommand::class, $ref->getName());
    }

    public function test_extends_illuminate_console_command(): void
    {
        $parents = class_parents(ExtensionRollbackCommand::class);

        $this->assertContains(Command::class, $parents);
    }

    public function test_signature_contains_command_name(): void
    {
        $ref = new \ReflectionProperty(ExtensionRollbackCommand::class, 'signature');
        $defaultValue = $ref->getDefaultValue();

        $this->assertStringContainsString('admin:ext-rollback', $defaultValue);
    }

    public function test_signature_contains_name_and_ver_arguments_and_force_option(): void
    {
        $ref = new \ReflectionProperty(ExtensionRollbackCommand::class, 'signature');
        $defaultValue = $ref->getDefaultValue();

        $this->assertStringContainsString('{name', $defaultValue);
        $this->assertStringContainsString('{ver', $defaultValue);
        $this->assertStringContainsString('--force', $defaultValue);
    }

    public function test_description_default_value(): void
    {
        $ref = new \ReflectionProperty(ExtensionRollbackCommand::class, 'description');
        $defaultValue = $ref->getDefaultValue();

        $this->assertEquals('Rollback an existing extension', $defaultValue);
    }

    public function test_handle_method_signature(): void
    {
        $method = new \ReflectionMethod(ExtensionRollbackCommand::class, 'handle');

        $this->assertSame(0, $method->getNumberOfParameters());
    }

    public function test_handle_is_public(): void
    {
        $ref = new \ReflectionMethod(ExtensionRollbackCommand::class, 'handle');

        $this->assertTrue($ref->isPublic());
    }
}
