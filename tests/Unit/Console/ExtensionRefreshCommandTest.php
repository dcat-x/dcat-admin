<?php

namespace Dcat\Admin\Tests\Unit\Console;

use Dcat\Admin\Console\ExtensionRefreshCommand;
use Dcat\Admin\Tests\TestCase;
use Illuminate\Console\Command;
use Mockery;

class ExtensionRefreshCommandTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }

    public function test_class_exists(): void
    {
        $this->assertTrue(class_exists(ExtensionRefreshCommand::class));
    }

    public function test_extends_illuminate_console_command(): void
    {
        $this->assertTrue(is_subclass_of(ExtensionRefreshCommand::class, Command::class));
    }

    public function test_signature_contains_command_name(): void
    {
        $ref = new \ReflectionProperty(ExtensionRefreshCommand::class, 'signature');
        $defaultValue = $ref->getDefaultValue();

        $this->assertStringContainsString('admin:ext-refresh', $defaultValue);
    }

    public function test_signature_contains_name_argument_and_path_option(): void
    {
        $ref = new \ReflectionProperty(ExtensionRefreshCommand::class, 'signature');
        $defaultValue = $ref->getDefaultValue();

        $this->assertStringContainsString('{name', $defaultValue);
        $this->assertStringContainsString('--path=', $defaultValue);
    }

    public function test_description_default_value(): void
    {
        $ref = new \ReflectionProperty(ExtensionRefreshCommand::class, 'description');
        $defaultValue = $ref->getDefaultValue();

        $this->assertEquals('Removes and re-adds an existing extension', $defaultValue);
    }

    public function test_has_handle_method(): void
    {
        $this->assertTrue(
            method_exists(ExtensionRefreshCommand::class, 'handle'),
            'ExtensionRefreshCommand should have method handle'
        );
    }

    public function test_handle_is_public(): void
    {
        $ref = new \ReflectionMethod(ExtensionRefreshCommand::class, 'handle');

        $this->assertTrue($ref->isPublic());
    }
}
