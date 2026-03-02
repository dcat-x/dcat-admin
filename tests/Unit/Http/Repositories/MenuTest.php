<?php

namespace Dcat\Admin\Tests\Unit\Http\Repositories;

use Dcat\Admin\Http\Repositories\Menu;
use Dcat\Admin\Repositories\EloquentRepository;
use Dcat\Admin\Tests\TestCase;
use Mockery;

class MenuTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_class_exists(): void
    {
        $this->assertTrue(class_exists(Menu::class));
    }

    public function test_is_subclass_of_eloquent_repository(): void
    {
        $this->assertTrue(is_subclass_of(Menu::class, EloquentRepository::class));
    }

    public function test_constructor_exists(): void
    {
        $this->assertTrue(method_exists(Menu::class, '__construct'));
    }
}
