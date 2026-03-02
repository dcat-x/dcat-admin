<?php

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

    public function test_class_exists(): void
    {
        $this->assertTrue(class_exists(ResetPasswordCommand::class));
    }

    public function test_extends_illuminate_console_command(): void
    {
        $this->assertTrue(is_subclass_of(ResetPasswordCommand::class, Command::class));
    }

    public function test_signature_default_value(): void
    {
        $ref = new \ReflectionProperty(ResetPasswordCommand::class, 'signature');
        $defaultValue = $ref->getDefaultValue();

        $this->assertEquals('admin:reset-password', $defaultValue);
    }

    public function test_description_default_value(): void
    {
        $ref = new \ReflectionProperty(ResetPasswordCommand::class, 'description');
        $defaultValue = $ref->getDefaultValue();

        $this->assertEquals('Reset password for a specific admin user', $defaultValue);
    }

    public function test_has_handle_method(): void
    {
        $this->assertTrue(
            method_exists(ResetPasswordCommand::class, 'handle'),
            'ResetPasswordCommand should have method "handle"'
        );
    }

    public function test_handle_is_public(): void
    {
        $ref = new \ReflectionMethod(ResetPasswordCommand::class, 'handle');

        $this->assertTrue($ref->isPublic());
    }
}
