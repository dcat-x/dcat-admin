<?php

namespace Dcat\Admin\Tests\Unit\Console;

use Dcat\Admin\Console\InstallCommand;
use Dcat\Admin\Tests\TestCase;
use Illuminate\Console\Command;
use Mockery;
use PHPUnit\Framework\Attributes\DataProvider;

class InstallCommandTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }

    public function test_can_be_instantiated(): void
    {
        $this->assertInstanceOf(InstallCommand::class, new InstallCommand);
    }

    public function test_extends_illuminate_console_command(): void
    {
        $parents = class_parents(InstallCommand::class);

        $this->assertContains(Command::class, $parents);
    }

    public function test_signature_default_value(): void
    {
        $ref = new \ReflectionProperty(InstallCommand::class, 'signature');
        $defaultValue = $ref->getDefaultValue();

        $this->assertSame('admin:install', $defaultValue);
    }

    public function test_description_default_value(): void
    {
        $ref = new \ReflectionProperty(InstallCommand::class, 'description');
        $defaultValue = $ref->getDefaultValue();

        $this->assertSame('Install the admin package', $defaultValue);
    }

    public function test_directory_property_exists_and_is_protected(): void
    {
        $this->assertTrue(property_exists(InstallCommand::class, 'directory'));

        $ref = new \ReflectionProperty(InstallCommand::class, 'directory');

        $this->assertTrue($ref->isProtected());
    }

    public function test_directory_default_value_is_empty_string(): void
    {
        $ref = new \ReflectionProperty(InstallCommand::class, 'directory');
        $defaultValue = $ref->getDefaultValue();

        $this->assertSame('', $defaultValue);
    }

    #[DataProvider('requiredMethodProvider')]
    public function test_has_required_methods(string $method): void
    {
        $reflection = new \ReflectionMethod(InstallCommand::class, $method);

        $this->assertSame($method, $reflection->getName());
    }

    #[DataProvider('publicMethodProvider')]
    public function test_public_methods_visibility(string $method): void
    {
        $ref = new \ReflectionMethod(InstallCommand::class, $method);

        $this->assertTrue($ref->isPublic());
    }

    #[DataProvider('protectedMethodProvider')]
    public function test_protected_methods_visibility(string $method): void
    {
        $ref = new \ReflectionMethod(InstallCommand::class, $method);

        $this->assertTrue($ref->isProtected());
    }

    public static function requiredMethodProvider(): array
    {
        return [
            ['handle'],
            ['initDatabase'],
            ['setDirectory'],
            ['initAdminDirectory'],
            ['createHomeController'],
            ['createAuthController'],
            ['createMetricCards'],
            ['namespace'],
            ['createBootstrapFile'],
            ['createRoutesFile'],
            ['getStub'],
            ['makeDir'],
        ];
    }

    public static function publicMethodProvider(): array
    {
        return [
            ['initDatabase'],
            ['createHomeController'],
            ['createAuthController'],
            ['createMetricCards'],
        ];
    }

    public static function protectedMethodProvider(): array
    {
        return [
            ['setDirectory'],
            ['getStub'],
        ];
    }
}
