<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Console;

use Dcat\Admin\Console\ExportSeedCommand;
use Dcat\Admin\Tests\TestCase;
use Illuminate\Console\Command;
use Mockery;
use PHPUnit\Framework\Attributes\DataProvider;

class ExportSeedCommandTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }

    public function test_reflection_can_load_class_metadata(): void
    {
        $ref = new \ReflectionClass(ExportSeedCommand::class);

        $this->assertSame(ExportSeedCommand::class, $ref->getName());
    }

    public function test_extends_illuminate_console_command(): void
    {
        $parents = class_parents(ExportSeedCommand::class);

        $this->assertContains(Command::class, $parents);
    }

    public function test_signature_contains_admin_export_seed(): void
    {
        $ref = new \ReflectionProperty(ExportSeedCommand::class, 'signature');
        $defaultValue = $ref->getDefaultValue();

        $this->assertStringContainsString('admin:export-seed', $defaultValue);
    }

    public function test_signature_contains_classname_argument_with_default(): void
    {
        $ref = new \ReflectionProperty(ExportSeedCommand::class, 'signature');
        $defaultValue = $ref->getDefaultValue();

        $this->assertStringContainsString('{classname=AdminTablesSeeder}', $defaultValue);
    }

    public function test_signature_contains_users_option(): void
    {
        $ref = new \ReflectionProperty(ExportSeedCommand::class, 'signature');
        $defaultValue = $ref->getDefaultValue();

        $this->assertStringContainsString('--users', $defaultValue);
    }

    public function test_description_contains_export_seed(): void
    {
        $ref = new \ReflectionProperty(ExportSeedCommand::class, 'description');
        $defaultValue = $ref->getDefaultValue();

        $this->assertStringContainsString('Export seed', $defaultValue);
    }

    #[DataProvider('requiredMethodProvider')]
    public function test_has_required_methods(string $method): void
    {
        $reflection = new \ReflectionMethod(ExportSeedCommand::class, $method);

        $this->assertSame($method, $reflection->getName());
    }

    #[DataProvider('protectedMethodProvider')]
    public function test_protected_methods(string $method): void
    {
        $ref = new \ReflectionMethod(ExportSeedCommand::class, $method);

        $this->assertTrue($ref->isProtected());
    }

    public static function requiredMethodProvider(): array
    {
        return [
            ['handle'],
            ['getTableName'],
            ['getTableDataArrayAsString'],
            ['getStub'],
            ['varExport'],
        ];
    }

    public static function protectedMethodProvider(): array
    {
        return [
            ['getTableName'],
            ['getTableDataArrayAsString'],
            ['varExport'],
        ];
    }
}
