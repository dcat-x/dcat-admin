<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Console;

use Dcat\Admin\Console\ResetPasswordCommand;
use Dcat\Admin\Tests\TestCase;
use Illuminate\Console\Command;
use Mockery;

class ResetPasswordCommandTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }

    public function test_can_be_instantiated(): void
    {
        $this->assertInstanceOf(ResetPasswordCommand::class, new ResetPasswordCommand);
    }

    public function test_extends_illuminate_console_command(): void
    {
        $parents = class_parents(ResetPasswordCommand::class);

        $this->assertContains(Command::class, $parents);
    }

    public function test_signature_default_value(): void
    {
        $ref = new \ReflectionProperty(ResetPasswordCommand::class, 'signature');
        $defaultValue = $ref->getDefaultValue();

        $this->assertSame('admin:reset-password', $defaultValue);
    }

    public function test_description_default_value(): void
    {
        $ref = new \ReflectionProperty(ResetPasswordCommand::class, 'description');
        $defaultValue = $ref->getDefaultValue();

        $this->assertSame('Reset password for a specific admin user', $defaultValue);
    }

    public function test_handle_method_signature(): void
    {
        $method = new \ReflectionMethod(ResetPasswordCommand::class, 'handle');

        $this->assertSame(0, $method->getNumberOfParameters());
    }

    public function test_handle_is_public(): void
    {
        $ref = new \ReflectionMethod(ResetPasswordCommand::class, 'handle');

        $this->assertTrue($ref->isPublic());
    }
}
