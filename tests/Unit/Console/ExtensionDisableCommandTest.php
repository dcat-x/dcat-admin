<?php

namespace Dcat\Admin\Tests\Unit\Console;

use Dcat\Admin\Console\ExtensionDiableCommand;
use Dcat\Admin\Tests\TestCase;
use Illuminate\Console\Command;
use Mockery;

class ExtensionDisableCommandTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }

    public function test_class_exists(): void
    {
        $this->assertTrue(class_exists(ExtensionDiableCommand::class));
    }

    public function test_extends_command(): void
    {
        $this->assertTrue(is_subclass_of(ExtensionDiableCommand::class, Command::class));
    }

    public function test_signature_contains_admin_ext_disable(): void
    {
        $ref = new \ReflectionProperty(ExtensionDiableCommand::class, 'signature');
        $defaultValue = $ref->getDefaultValue();

        $this->assertStringContainsString('admin:ext-disable', $defaultValue);
    }

    public function test_signature_contains_name_argument(): void
    {
        $ref = new \ReflectionProperty(ExtensionDiableCommand::class, 'signature');
        $defaultValue = $ref->getDefaultValue();

        $this->assertStringContainsString('{name', $defaultValue);
    }

    public function test_description_default_value(): void
    {
        $ref = new \ReflectionProperty(ExtensionDiableCommand::class, 'description');
        $defaultValue = $ref->getDefaultValue();

        $this->assertEquals('Disable an existing extension', $defaultValue);
    }

    public function test_handle_method_exists(): void
    {
        $this->assertTrue(method_exists(ExtensionDiableCommand::class, 'handle'));
    }

    public function test_handle_is_public(): void
    {
        $ref = new \ReflectionMethod(ExtensionDiableCommand::class, 'handle');

        $this->assertTrue($ref->isPublic());
    }
}
