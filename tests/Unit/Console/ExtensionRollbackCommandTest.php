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

    public function test_class_exists(): void
    {
        $this->assertTrue(class_exists(ExtensionRollbackCommand::class));
    }

    public function test_extends_illuminate_console_command(): void
    {
        $this->assertTrue(is_subclass_of(ExtensionRollbackCommand::class, Command::class));
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

    public function test_has_handle_method(): void
    {
        $this->assertTrue(
            method_exists(ExtensionRollbackCommand::class, 'handle'),
            'ExtensionRollbackCommand should have method handle'
        );
    }

    public function test_handle_is_public(): void
    {
        $ref = new \ReflectionMethod(ExtensionRollbackCommand::class, 'handle');

        $this->assertTrue($ref->isPublic());
    }
}
