<?php

namespace Dcat\Admin\Tests\Unit\Console;

use Dcat\Admin\Console\HealthCheckCommand;
use Dcat\Admin\Tests\TestCase;
use Illuminate\Console\Command;

class HealthCheckCommandTest extends TestCase
{
    public function test_can_be_instantiated(): void
    {
        $this->assertInstanceOf(HealthCheckCommand::class, new HealthCheckCommand);
    }

    public function test_extends_illuminate_console_command(): void
    {
        $parents = class_parents(HealthCheckCommand::class);

        $this->assertContains(Command::class, $parents);
    }

    public function test_signature_default_value(): void
    {
        $ref = new \ReflectionProperty(HealthCheckCommand::class, 'signature');

        $signature = $ref->getDefaultValue();
        $this->assertStringContainsString('admin:health-check', $signature);
        $this->assertStringContainsString('{--json : Output issues as JSON}', $signature);
        $this->assertStringContainsString('{--scope=all : Check scope: all|menu|permission}', $signature);
        $this->assertStringContainsString('{--fail-on=warning : Exit non-zero on: never|warning|error}', $signature);
        $this->assertStringContainsString('{--refresh : Bypass health-check cache}', $signature);
        $this->assertStringContainsString('{--quiet : Do not print issue details}', $signature);
    }

    public function test_description_default_value(): void
    {
        $ref = new \ReflectionProperty(HealthCheckCommand::class, 'description');

        $this->assertSame('Check admin menu/permission configuration health', $ref->getDefaultValue());
    }

    public function test_resolve_exit_code(): void
    {
        $command = new HealthCheckCommand;
        $method = new \ReflectionMethod(HealthCheckCommand::class, 'resolveExitCode');
        $method->setAccessible(true);

        $this->assertSame(0, $method->invoke($command, [], 'warning'));
        $this->assertSame(1, $method->invoke($command, [['severity' => 'warning']], 'warning'));
        $this->assertSame(0, $method->invoke($command, [['severity' => 'warning']], 'error'));
        $this->assertSame(1, $method->invoke($command, [['severity' => 'error']], 'error'));
        $this->assertSame(0, $method->invoke($command, [['severity' => 'error']], 'never'));
    }
}
