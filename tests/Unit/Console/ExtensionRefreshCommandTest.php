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

    public function test_reflection_can_load_class_metadata(): void
    {
        $ref = new \ReflectionClass(ExtensionRefreshCommand::class);

        $this->assertSame(ExtensionRefreshCommand::class, $ref->getName());
    }

    public function test_extends_illuminate_console_command(): void
    {
        $parents = class_parents(ExtensionRefreshCommand::class);

        $this->assertContains(Command::class, $parents);
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

    public function test_handle_method_signature(): void
    {
        $method = new \ReflectionMethod(ExtensionRefreshCommand::class, 'handle');

        $this->assertSame(0, $method->getNumberOfParameters());
    }

    public function test_handle_is_public(): void
    {
        $ref = new \ReflectionMethod(ExtensionRefreshCommand::class, 'handle');

        $this->assertTrue($ref->isPublic());
    }
}
