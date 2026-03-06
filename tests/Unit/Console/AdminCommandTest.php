<?php

namespace Dcat\Admin\Tests\Unit\Console;

use Dcat\Admin\Console\AdminCommand;
use Dcat\Admin\Tests\TestCase;
use Illuminate\Console\Command;
use Mockery;
use PHPUnit\Framework\Attributes\DataProvider;

class AdminCommandTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }

    public function test_reflection_can_load_class_metadata(): void
    {
        $ref = new \ReflectionClass(AdminCommand::class);

        $this->assertSame(AdminCommand::class, $ref->getName());
    }

    public function test_extends_illuminate_console_command(): void
    {
        $parents = class_parents(AdminCommand::class);

        $this->assertContains(Command::class, $parents);
    }

    public function test_signature_default_value(): void
    {
        $ref = new \ReflectionProperty(AdminCommand::class, 'signature');
        $defaultValue = $ref->getDefaultValue();

        $this->assertEquals('admin', $defaultValue);
    }

    public function test_description_default_value(): void
    {
        $ref = new \ReflectionProperty(AdminCommand::class, 'description');
        $defaultValue = $ref->getDefaultValue();

        $this->assertEquals('List all admin commands', $defaultValue);
    }

    public function test_has_static_logo_property(): void
    {
        $this->assertTrue(property_exists(AdminCommand::class, 'logo'));

        $ref = new \ReflectionProperty(AdminCommand::class, 'logo');

        $this->assertTrue($ref->isPublic());
        $this->assertTrue($ref->isStatic());
    }

    public function test_logo_is_ascii_art_string(): void
    {
        $this->assertIsString(AdminCommand::$logo);
        $this->assertNotEmpty(AdminCommand::$logo);
        // The logo is ASCII art that visually renders "DCAT ADMIN"
        $this->assertStringContainsString('____', AdminCommand::$logo);
        $this->assertStringContainsString('/', AdminCommand::$logo);
    }

    public function test_logo_contains_multiple_lines(): void
    {
        $lines = explode("\n", AdminCommand::$logo);

        $this->assertGreaterThan(3, count($lines));
    }

    #[DataProvider('requiredMethodProvider')]
    public function test_has_required_methods(string $method): void
    {
        $reflection = new \ReflectionMethod(AdminCommand::class, $method);

        $this->assertSame($method, $reflection->getName());
    }

    public function test_strlen_is_public_static(): void
    {
        $ref = new \ReflectionMethod(AdminCommand::class, 'strlen');

        $this->assertTrue($ref->isPublic());
        $this->assertTrue($ref->isStatic());
    }

    public function test_strlen_with_ascii_string(): void
    {
        $result = AdminCommand::strlen('hello');

        $this->assertIsInt($result);
        $this->assertEquals(5, $result);
    }

    public function test_strlen_with_empty_string(): void
    {
        $result = AdminCommand::strlen('');

        $this->assertIsInt($result);
        $this->assertEquals(0, $result);
    }

    public function test_handle_is_public(): void
    {
        $ref = new \ReflectionMethod(AdminCommand::class, 'handle');

        $this->assertTrue($ref->isPublic());
    }

    public static function requiredMethodProvider(): array
    {
        return [
            ['handle'],
            ['listAdminCommands'],
            ['getColumnWidth'],
            ['strlen'],
        ];
    }
}
