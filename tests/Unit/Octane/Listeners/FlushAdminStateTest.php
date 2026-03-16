<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Octane\Listeners;

use Dcat\Admin\Octane\Listeners\FlushAdminState;
use Dcat\Admin\Tests\TestCase;

class FlushAdminStateTest extends TestCase
{
    public function test_constructor_sets_app(): void
    {
        $listener = new FlushAdminState($this->app);

        $reflection = new \ReflectionProperty(FlushAdminState::class, 'app');
        $reflection->setAccessible(true);
        $this->assertSame($this->app, $reflection->getValue($listener));
    }

    public function test_admin_services_is_array(): void
    {
        $listener = new FlushAdminState($this->app);

        $reflection = new \ReflectionProperty(FlushAdminState::class, 'adminServices');
        $reflection->setAccessible(true);
        $services = $reflection->getValue($listener);

        $this->assertIsArray($services);
        $this->assertNotEmpty($services);
    }

    public function test_admin_services_contains_expected_keys(): void
    {
        $listener = new FlushAdminState($this->app);

        $reflection = new \ReflectionProperty(FlushAdminState::class, 'adminServices');
        $reflection->setAccessible(true);
        $services = $reflection->getValue($listener);

        $this->assertContains('admin.app', $services);
        $this->assertContains('admin.asset', $services);
        $this->assertContains('admin.color', $services);
        $this->assertContains('admin.context', $services);
        $this->assertContains('admin.menu', $services);
    }

    public function test_handle_method_signature(): void
    {
        $method = new \ReflectionMethod(FlushAdminState::class, 'handle');

        $this->assertSame(1, $method->getNumberOfParameters());
    }
}
