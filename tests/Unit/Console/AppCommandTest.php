<?php

namespace Dcat\Admin\Tests\Unit\Console;

use Dcat\Admin\Console\AppCommand;
use Dcat\Admin\Console\InstallCommand;
use Dcat\Admin\Tests\TestCase;
use Mockery;
use PHPUnit\Framework\Attributes\DataProvider;

class AppCommandTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }

    public function test_reflection_can_load_class_metadata(): void
    {
        $ref = new \ReflectionClass(AppCommand::class);

        $this->assertSame(AppCommand::class, $ref->getName());
    }

    public function test_extends_install_command(): void
    {
        $parents = class_parents(AppCommand::class);

        $this->assertContains(InstallCommand::class, $parents);
    }

    public function test_signature_default_value(): void
    {
        $ref = new \ReflectionProperty(AppCommand::class, 'signature');
        $defaultValue = $ref->getDefaultValue();

        $this->assertSame('admin:app {name}', $defaultValue);
    }

    public function test_description_default_value(): void
    {
        $ref = new \ReflectionProperty(AppCommand::class, 'description');
        $defaultValue = $ref->getDefaultValue();

        $this->assertSame('Create new application', $defaultValue);
    }

    #[DataProvider('requiredMethodProvider')]
    public function test_has_required_methods(string $method): void
    {
        $reflection = new \ReflectionMethod(AppCommand::class, $method);

        $this->assertSame($method, $reflection->getName());
    }

    public function test_add_config_is_protected(): void
    {
        $ref = new \ReflectionMethod(AppCommand::class, 'addConfig');

        $this->assertTrue($ref->isProtected());
    }

    public function test_set_directory_is_protected(): void
    {
        $ref = new \ReflectionMethod(AppCommand::class, 'setDirectory');

        $this->assertTrue($ref->isProtected());
    }

    public function test_handle_is_public(): void
    {
        $ref = new \ReflectionMethod(AppCommand::class, 'handle');

        $this->assertTrue($ref->isPublic());
    }

    public static function requiredMethodProvider(): array
    {
        return [
            ['handle'],
            ['addConfig'],
            ['setDirectory'],
        ];
    }
}
