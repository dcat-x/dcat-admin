<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Console;

use Dcat\Admin\Console\PublishCommand;
use Dcat\Admin\Tests\TestCase;
use Illuminate\Console\Command;
use Mockery;
use PHPUnit\Framework\Attributes\DataProvider;

class PublishCommandTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }

    public function test_constructor_accepts_filesystem_parameter(): void
    {
        $method = new \ReflectionMethod(PublishCommand::class, '__construct');
        $params = $method->getParameters();

        $this->assertCount(1, $params);
        $this->assertSame('files', $params[0]->getName());
        $this->assertSame('Illuminate\Filesystem\Filesystem', $params[0]->getType()?->getName());
    }

    public function test_extends_illuminate_console_command(): void
    {
        $parents = class_parents(PublishCommand::class);

        $this->assertContains(Command::class, $parents);
    }

    public function test_signature_contains_admin_publish(): void
    {
        $ref = new \ReflectionProperty(PublishCommand::class, 'signature');
        $defaultValue = $ref->getDefaultValue();

        $this->assertStringContainsString('admin:publish', $defaultValue);
    }

    public function test_signature_contains_expected_options(): void
    {
        $ref = new \ReflectionProperty(PublishCommand::class, 'signature');
        $defaultValue = $ref->getDefaultValue();

        $this->assertStringContainsString('--force', $defaultValue);
        $this->assertStringContainsString('--lang', $defaultValue);
        $this->assertStringContainsString('--assets', $defaultValue);
        $this->assertStringContainsString('--migrations', $defaultValue);
        $this->assertStringContainsString('--config', $defaultValue);
    }

    public function test_description_contains_re_publish(): void
    {
        $ref = new \ReflectionProperty(PublishCommand::class, 'description');
        $defaultValue = $ref->getDefaultValue();

        $this->assertStringContainsString('Re-publish', $defaultValue);
    }

    public function test_tags_default_is_empty_array(): void
    {
        $ref = new \ReflectionProperty(PublishCommand::class, 'tags');
        $defaultValue = $ref->getDefaultValue();

        $this->assertIsArray($defaultValue);
        $this->assertEmpty($defaultValue);
    }

    #[DataProvider('requiredMethodProvider')]
    public function test_has_all_required_methods(string $method): void
    {
        $reflection = new \ReflectionMethod(PublishCommand::class, $method);

        $this->assertSame($method, $reflection->getName());
    }

    public function test_handle_is_public(): void
    {
        $ref = new \ReflectionMethod(PublishCommand::class, 'handle');

        $this->assertTrue($ref->isPublic());
    }

    #[DataProvider('protectedMethodProvider')]
    public function test_methods_are_protected(string $method): void
    {
        $ref = new \ReflectionMethod(PublishCommand::class, $method);

        $this->assertTrue($ref->isProtected());
    }

    public function test_files_property_exists(): void
    {
        $this->assertTrue(property_exists(PublishCommand::class, 'files'));

        $ref = new \ReflectionProperty(PublishCommand::class, 'files');

        $this->assertTrue($ref->isProtected());
    }

    public static function requiredMethodProvider(): array
    {
        return [
            ['handle'],
            ['getTags'],
            ['publishTag'],
            ['pathsToPublish'],
            ['publishItem'],
            ['publishFile'],
            ['publishDirectory'],
            ['moveManagedFiles'],
            ['isExceptPath'],
            ['createParentDirectory'],
            ['status'],
        ];
    }

    public static function protectedMethodProvider(): array
    {
        return [
            ['getTags'],
            ['publishTag'],
            ['isExceptPath'],
        ];
    }
}
