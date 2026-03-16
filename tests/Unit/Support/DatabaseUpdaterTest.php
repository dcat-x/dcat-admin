<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Support;

use Dcat\Admin\Support\DatabaseUpdater;
use Dcat\Admin\Tests\TestCase;
use Illuminate\Support\Facades\DB;
use Mockery;

class DatabaseUpdaterTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    protected function makeUpdater(): DatabaseUpdater
    {
        return new DatabaseUpdater;
    }

    public function test_resolve_returns_object_when_given_object(): void
    {
        $updater = $this->makeUpdater();
        $obj = new \stdClass;
        $this->assertSame($obj, $updater->resolve($obj));
    }

    public function test_resolve_returns_null_for_nonexistent_file(): void
    {
        $updater = $this->makeUpdater();
        $this->assertNull($updater->resolve('/nonexistent/file.php'));
    }

    public function test_connection_returns_string(): void
    {
        $updater = $this->makeUpdater();
        $this->assertIsString($updater->connection());
    }

    public function test_connection_uses_config(): void
    {
        $updater = $this->makeUpdater();
        $connection = $updater->connection();

        $expected = config('admin.database.connection') ?: config('database.default');
        $this->assertSame($expected, $connection);
    }

    public function test_get_class_from_file_extracts_namespace_and_class(): void
    {
        $updater = $this->makeUpdater();
        $file = tempnam(sys_get_temp_dir(), 'migration_');
        file_put_contents($file, <<<'PHP'
<?php
namespace Tests\Migrations;
class CreateUsersTable {}
PHP
        );

        try {
            $class = $updater->getClassFromFile($file);
        } finally {
            @unlink($file);
        }

        $this->assertSame('Tests\Migrations\CreateUsersTable', $class);
    }

    public function test_set_up_returns_false_for_nonexistent_file(): void
    {
        $updater = $this->makeUpdater();
        $result = $updater->setUp('/nonexistent/migration.php');
        $this->assertFalse($result);
    }

    public function test_pack_down_returns_false_for_nonexistent_file(): void
    {
        $updater = $this->makeUpdater();
        $result = $updater->packDown('/nonexistent/migration.php');
        $this->assertFalse($result);
    }

    public function test_is_valid_script_method_exists(): void
    {
        $method = new \ReflectionMethod(DatabaseUpdater::class, 'isValidScript');
        $this->assertTrue($method->isProtected());
    }

    public function test_transaction_delegates_to_connection_and_returns_callback_value(): void
    {
        $updater = $this->makeUpdater();
        $connection = Mockery::mock();
        $connection->shouldReceive('transaction')
            ->once()
            ->andReturnUsing(fn ($callback) => $callback());

        DB::shouldReceive('connection')
            ->once()
            ->with($updater->connection())
            ->andReturn($connection);

        $result = $updater->transaction(fn () => 'ok');

        $this->assertSame('ok', $result);
    }
}
