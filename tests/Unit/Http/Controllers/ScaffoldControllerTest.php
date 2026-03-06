<?php

namespace Dcat\Admin\Tests\Unit\Http\Controllers;

use Dcat\Admin\Http\Controllers\ScaffoldController;
use Dcat\Admin\Tests\TestCase;
use Illuminate\Support\Facades\DB;

class ScaffoldControllerTest extends TestCase
{
    public function test_db_types_is_not_empty(): void
    {
        $this->assertNotEmpty(ScaffoldController::$dbTypes);
        $this->assertContains('string', ScaffoldController::$dbTypes);
        $this->assertContains('integer', ScaffoldController::$dbTypes);
        $this->assertContains('text', ScaffoldController::$dbTypes);
        $this->assertContains('boolean', ScaffoldController::$dbTypes);
        $this->assertContains('json', ScaffoldController::$dbTypes);
    }

    public function test_data_type_map_is_not_empty(): void
    {
        $this->assertNotEmpty(ScaffoldController::$dataTypeMap);
        $this->assertSame('integer', ScaffoldController::$dataTypeMap['int']);
        $this->assertSame('string', ScaffoldController::$dataTypeMap['varchar']);
        $this->assertSame('text', ScaffoldController::$dataTypeMap['text']);
        $this->assertSame('dateTime', ScaffoldController::$dataTypeMap['datetime']);
    }

    public function test_data_type_map_covers_unsigned_types(): void
    {
        $this->assertSame('unsignedInteger', ScaffoldController::$dataTypeMap['int@unsigned']);
        $this->assertSame('unsignedTinyInteger', ScaffoldController::$dataTypeMap['tinyint@unsigned']);
        $this->assertSame('unsignedSmallInteger', ScaffoldController::$dataTypeMap['smallint@unsigned']);
        $this->assertSame('unsignedBigInteger', ScaffoldController::$dataTypeMap['bigint@unsigned']);
    }

    public function test_get_database_columns_uses_parameterized_bindings(): void
    {
        // 配置一个 MySQL 类型的连接（不需要真正连接）
        $this->app['config']->set('database.connections.scaffold_test', [
            'driver' => 'mysql',
            'database' => 'test_db',
            'host' => '127.0.0.1',
            'port' => '3306',
            'username' => 'root',
            'password' => '',
            'prefix' => 'pre_',
        ]);

        $controller = new class extends ScaffoldController
        {
            public function exposeGetDatabaseColumns($db = null, $tb = null)
            {
                return $this->getDatabaseColumns($db, $tb);
            }
        };

        // Mock DB connection 来捕获 SQL 和绑定参数
        $capturedSql = null;
        $capturedBindings = null;

        $connection = \Mockery::mock(\Illuminate\Database\Connection::class);
        $connection->shouldReceive('select')
            ->once()
            ->withArgs(function ($sql, $bindings) use (&$capturedSql, &$capturedBindings) {
                $capturedSql = $sql;
                $capturedBindings = $bindings;

                return true;
            })
            ->andReturn([]);

        DB::shouldReceive('connection')
            ->with('scaffold_test')
            ->andReturn($connection);

        $result = $controller->exposeGetDatabaseColumns('test_db', 'users');

        // 验证使用了参数化查询
        $this->assertStringContainsString('table_schema = ?', $capturedSql);
        $this->assertStringContainsString('TABLE_NAME = ?', $capturedSql);

        // 验证绑定参数正确
        $this->assertSame('test_db', $capturedBindings[0]);
        $this->assertSame('pre_users', $capturedBindings[1]);

        // 验证没有不安全的字符串拼接
        $this->assertStringNotContainsString('table_schema = "test_db"', $capturedSql);
    }

    public function test_get_database_columns_without_table_name(): void
    {
        $this->app['config']->set('database.connections.scaffold_test2', [
            'driver' => 'mysql',
            'database' => 'test_db2',
            'host' => '127.0.0.1',
            'port' => '3306',
            'username' => 'root',
            'password' => '',
        ]);

        $controller = new class extends ScaffoldController
        {
            public function exposeGetDatabaseColumns($db = null, $tb = null)
            {
                return $this->getDatabaseColumns($db, $tb);
            }
        };

        $capturedSql = null;
        $capturedBindings = null;

        $connection = \Mockery::mock(\Illuminate\Database\Connection::class);
        $connection->shouldReceive('select')
            ->once()
            ->withArgs(function ($sql, $bindings) use (&$capturedSql, &$capturedBindings) {
                $capturedSql = $sql;
                $capturedBindings = $bindings;

                return true;
            })
            ->andReturn([]);

        DB::shouldReceive('connection')
            ->with('scaffold_test2')
            ->andReturn($connection);

        // 不传表名时，绑定只有数据库名
        $result = $controller->exposeGetDatabaseColumns('test_db2', null);

        $this->assertStringContainsString('table_schema = ?', $capturedSql);
        $this->assertStringNotContainsString('TABLE_NAME = ?', $capturedSql);
        $this->assertCount(1, $capturedBindings);
        $this->assertSame('test_db2', $capturedBindings[0]);
    }
}
