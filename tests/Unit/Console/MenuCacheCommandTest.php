<?php

namespace Dcat\Admin\Tests\Unit\Console;

use Dcat\Admin\Console\MenuCacheCommand;
use Dcat\Admin\Tests\TestCase;
use Illuminate\Console\Command;
use Mockery;

class MenuCacheCommandTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }

    public function test_class_exists(): void
    {
        $this->assertTrue(class_exists(MenuCacheCommand::class));
    }

    public function test_extends_illuminate_console_command(): void
    {
        $this->assertTrue(is_subclass_of(MenuCacheCommand::class, Command::class));
    }

    public function test_signature_default_value(): void
    {
        $ref = new \ReflectionProperty(MenuCacheCommand::class, 'signature');
        $defaultValue = $ref->getDefaultValue();

        $this->assertEquals('admin:menu-cache', $defaultValue);
    }

    public function test_description_default_value(): void
    {
        $ref = new \ReflectionProperty(MenuCacheCommand::class, 'description');
        $defaultValue = $ref->getDefaultValue();

        $this->assertEquals('Flush the menu cache', $defaultValue);
    }

    public function test_has_handle_method(): void
    {
        $this->assertTrue(
            method_exists(MenuCacheCommand::class, 'handle'),
            'MenuCacheCommand should have method "handle"'
        );
    }

    public function test_handle_is_public(): void
    {
        $ref = new \ReflectionMethod(MenuCacheCommand::class, 'handle');

        $this->assertTrue($ref->isPublic());
    }
}
