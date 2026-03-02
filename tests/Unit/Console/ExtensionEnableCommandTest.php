<?php

namespace Dcat\Admin\Tests\Unit\Console;

use Dcat\Admin\Console\ExtensionEnableCommand;
use Dcat\Admin\Tests\TestCase;
use Illuminate\Console\Command;
use Mockery;

class ExtensionEnableCommandTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }

    public function test_class_exists(): void
    {
        $this->assertTrue(class_exists(ExtensionEnableCommand::class));
    }

    public function test_extends_command(): void
    {
        $this->assertTrue(is_subclass_of(ExtensionEnableCommand::class, Command::class));
    }

    public function test_signature_contains_admin_ext_enable(): void
    {
        $ref = new \ReflectionProperty(ExtensionEnableCommand::class, 'signature');
        $defaultValue = $ref->getDefaultValue();

        $this->assertStringContainsString('admin:ext-enable', $defaultValue);
    }

    public function test_signature_contains_name_argument(): void
    {
        $ref = new \ReflectionProperty(ExtensionEnableCommand::class, 'signature');
        $defaultValue = $ref->getDefaultValue();

        $this->assertStringContainsString('{name', $defaultValue);
    }

    public function test_description_default_value(): void
    {
        $ref = new \ReflectionProperty(ExtensionEnableCommand::class, 'description');
        $defaultValue = $ref->getDefaultValue();

        $this->assertEquals('Enable an existing extension', $defaultValue);
    }

    public function test_handle_method_exists(): void
    {
        $this->assertTrue(method_exists(ExtensionEnableCommand::class, 'handle'));
    }

    public function test_handle_is_public(): void
    {
        $ref = new \ReflectionMethod(ExtensionEnableCommand::class, 'handle');

        $this->assertTrue($ref->isPublic());
    }
}
