<?php

namespace Dcat\Admin\Tests\Unit\Extend;

use Dcat\Admin\Extend\HasExtension;
use Dcat\Admin\Extend\ServiceProvider;
use Dcat\Admin\Tests\TestCase;
use Mockery;

class HasExtensionTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    protected function createHasExtensionUser(): object
    {
        return new class
        {
            use HasExtension;
        };
    }

    public function test_set_extension_returns_self(): void
    {
        $user = $this->createHasExtensionUser();
        $provider = Mockery::mock(ServiceProvider::class);

        $result = $user->setExtension($provider);
        $this->assertSame($user, $result);
    }

    public function test_get_extension_returns_provider(): void
    {
        $user = $this->createHasExtensionUser();
        $provider = Mockery::mock(ServiceProvider::class);

        $user->setExtension($provider);
        $this->assertSame($provider, $user->getExtension());
    }

    public function test_get_extension_name_delegates_to_provider(): void
    {
        $user = $this->createHasExtensionUser();
        $provider = Mockery::mock(ServiceProvider::class);
        $provider->shouldReceive('getName')->once()->andReturn('test-extension');

        $user->setExtension($provider);
        $this->assertSame('test-extension', $user->getExtensionName());
    }

    public function test_extension_initially_null(): void
    {
        $user = $this->createHasExtensionUser();
        $reflection = new \ReflectionProperty($user, 'extension');
        $reflection->setAccessible(true);
        $this->assertNull($reflection->getValue($user));
    }
}
