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

    public function test_seeder_is_instance_of_illuminate_database_seeder(): void
    {
        $seeder = new AdminTablesSeeder;

        $this->assertInstanceOf(Seeder::class, $seeder);
    }

    public function test_run_method_signature_has_no_parameters(): void
    {
        $reflection = new \ReflectionMethod(AdminTablesSeeder::class, 'run');

        $this->assertCount(0, $reflection->getParameters());
    }

    public function test_run_method_is_public(): void
    {
        $reflection = new \ReflectionMethod(AdminTablesSeeder::class, 'run');
        $this->assertTrue($reflection->isPublic());
    }

    public function test_run_method_declared_in_seeder_class(): void
    {
        $reflection = new \ReflectionMethod(AdminTablesSeeder::class, 'run');
        $this->assertSame(AdminTablesSeeder::class, $reflection->getDeclaringClass()->getName());
    }
}
