<?php

namespace Dcat\Admin\Tests\Unit\Traits;

use Dcat\Admin\Contracts\LazyRenderable;
use Dcat\Admin\Tests\TestCase;
use Dcat\Admin\Traits\InteractsWithRenderApi;
use Mockery;

class InteractsWithRenderApiTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    protected function createTraitUser(): object
    {
        return new class
        {
            use InteractsWithRenderApi;

            public $target = 'modal';
        };
    }

    public function test_on_load_appends_script(): void
    {
        $user = $this->createTraitUser();
        $result = $user->onLoad('console.log("loaded")');

        $this->assertSame($user, $result);

        $reflection = new \ReflectionProperty($user, 'loadScript');
        $reflection->setAccessible(true);
        $this->assertStringContainsString('console.log("loaded")', $reflection->getValue($user));
    }

    public function test_on_load_concatenates_scripts(): void
    {
        $user = $this->createTraitUser();
        $user->onLoad('first()');
        $user->onLoad('second()');

        $reflection = new \ReflectionProperty($user, 'loadScript');
        $reflection->setAccessible(true);
        $script = $reflection->getValue($user);

        $this->assertStringContainsString('first()', $script);
        $this->assertStringContainsString('second()', $script);
    }

    public function test_get_renderable_returns_null_by_default(): void
    {
        $user = $this->createTraitUser();
        $this->assertNull($user->getRenderable());
    }

    public function test_set_renderable_and_get_renderable(): void
    {
        $user = $this->createTraitUser();
        $renderable = Mockery::mock(LazyRenderable::class);

        $result = $user->setRenderable($renderable);

        $this->assertSame($user, $result);
        $this->assertSame($renderable, $user->getRenderable());
    }

    public function test_set_renderable_with_null(): void
    {
        $user = $this->createTraitUser();
        $user->setRenderable(null);

        $this->assertNull($user->getRenderable());
    }
}
