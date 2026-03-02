<?php

namespace Dcat\Admin\Tests\Unit\Models;

use Dcat\Admin\Models\AdminTablesSeeder;
use Dcat\Admin\Tests\TestCase;
use Illuminate\Database\Seeder;
use Mockery;

class AdminTablesSeederTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_class_exists(): void
    {
        $this->assertTrue(class_exists(AdminTablesSeeder::class));
    }

    public function test_is_subclass_of_seeder(): void
    {
        $this->assertTrue(is_subclass_of(AdminTablesSeeder::class, Seeder::class));
    }

    public function test_method_run_exists(): void
    {
        $this->assertTrue(method_exists(AdminTablesSeeder::class, 'run'));
    }

    public function test_run_method_is_public(): void
    {
        $reflection = new \ReflectionMethod(AdminTablesSeeder::class, 'run');
        $this->assertTrue($reflection->isPublic());
    }

    public function test_run_method_declared_in_seeder_class(): void
    {
        $reflection = new \ReflectionMethod(AdminTablesSeeder::class, 'run');
        $this->assertEquals(AdminTablesSeeder::class, $reflection->getDeclaringClass()->getName());
    }
}
