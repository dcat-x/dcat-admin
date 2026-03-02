<?php

namespace Dcat\Admin\Tests\Unit\Console;

use Dcat\Admin\Console\ExtensionInstallCommand;
use Dcat\Admin\Tests\TestCase;
use Illuminate\Console\Command;
use Mockery;

class ExtensionInstallCommandTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }

    public function test_class_exists(): void
    {
        $this->assertTrue(class_exists(ExtensionInstallCommand::class));
    }

    public function test_extends_command(): void
    {
        $this->assertTrue(is_subclass_of(ExtensionInstallCommand::class, Command::class));
    }

    public function test_signature_contains_admin_ext_install(): void
    {
        $ref = new \ReflectionProperty(ExtensionInstallCommand::class, 'signature');
        $defaultValue = $ref->getDefaultValue();

        $this->assertStringContainsString('admin:ext-install', $defaultValue);
    }

    public function test_signature_contains_name_argument_and_path_option(): void
    {
        $ref = new \ReflectionProperty(ExtensionInstallCommand::class, 'signature');
        $defaultValue = $ref->getDefaultValue();

        $this->assertStringContainsString('{name', $defaultValue);
        $this->assertStringContainsString('--path=', $defaultValue);
    }

    public function test_description_default_value(): void
    {
        $ref = new \ReflectionProperty(ExtensionInstallCommand::class, 'description');
        $defaultValue = $ref->getDefaultValue();

        $this->assertEquals('Install an extension', $defaultValue);
    }

    public function test_handle_method_exists(): void
    {
        $this->assertTrue(method_exists(ExtensionInstallCommand::class, 'handle'));
    }

    public function test_handle_is_public(): void
    {
        $ref = new \ReflectionMethod(ExtensionInstallCommand::class, 'handle');

        $this->assertTrue($ref->isPublic());
    }
}
