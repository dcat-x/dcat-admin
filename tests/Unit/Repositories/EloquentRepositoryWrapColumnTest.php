<?php

namespace Dcat\Admin\Tests\Unit\Repositories;

use Dcat\Admin\Repositories\EloquentRepository;
use Dcat\Admin\Tests\TestCase;
use Illuminate\Database\Eloquent\Model;

class WrapColumnTestModel extends Model
{
    protected $table = 'test_table';
}

class WrapColumnTestRepository extends EloquentRepository
{
    protected $eloquentClass = WrapColumnTestModel::class;
}

class EloquentRepositoryWrapColumnTest extends TestCase
{
    public function test_wrap_column_uses_grammar_wrap(): void
    {
        $repo = new WrapColumnTestRepository;

        $reflection = new \ReflectionMethod($repo, 'wrapMySqlColumn');
        $reflection->setAccessible(true);

        $result = $reflection->invoke($repo, 'column_name');

        // SQLite grammar 使用双引号包裹列名
        // 旧代码使用反引号 `column_name`，新代码委托给 grammar->wrap()
        $this->assertIsString($result);
        $this->assertStringContainsString('column_name', $result);

        // SQLite 使用双引号，不使用反引号 — 这里断言使用了双引号
        // 如果仍然是 `column_name`（反引号），说明还在硬编码
        $this->assertSame('"column_name"', $result);
    }

    public function test_wrap_column_handles_dotted_notation(): void
    {
        $repo = new WrapColumnTestRepository;

        $reflection = new \ReflectionMethod($repo, 'wrapMySqlColumn');
        $reflection->setAccessible(true);

        $result = $reflection->invoke($repo, 'table.column');

        // grammar->wrap('table.column') 会分别包裹表名和列名
        $this->assertIsString($result);
        $this->assertStringContainsString('table', $result);
        $this->assertStringContainsString('column', $result);

        // SQLite grammar 应产生 "table"."column"
        $this->assertSame('"table"."column"', $result);
    }

    public function test_wrap_column_delegates_to_connection_grammar(): void
    {
        $repo = new WrapColumnTestRepository;

        // 获取实际使用的 grammar
        $grammar = $repo->model()->getConnection()->getQueryGrammar();

        $reflection = new \ReflectionMethod($repo, 'wrapMySqlColumn');
        $reflection->setAccessible(true);

        $result = $reflection->invoke($repo, 'my_col');

        // 确认结果与直接调用 grammar->wrap() 一致
        $this->assertSame($grammar->wrap('my_col'), $result);
    }
}
