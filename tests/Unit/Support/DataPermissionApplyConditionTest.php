<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Support;

use Dcat\Admin\Models\Administrator;
use Dcat\Admin\Models\DataRule;
use Dcat\Admin\Support\DataPermission;
use Dcat\Admin\Tests\TestCase;
use Illuminate\Database\Eloquent\Builder;
use Mockery;

class DataPermissionApplyConditionTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->app['config']->set('admin.auth.guard', 'admin');
        $this->app['config']->set('auth.guards.admin', [
            'driver' => 'session',
            'provider' => 'admin',
        ]);
        $this->app['config']->set('auth.providers.admin', [
            'driver' => 'eloquent',
            'model' => Administrator::class,
        ]);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }

    protected function callApplyCondition(Builder $query, DataRule $rule): void
    {
        $dp = new DataPermission(null);
        $reflection = new \ReflectionMethod($dp, 'applyCondition');
        $reflection->setAccessible(true);
        $reflection->invoke($dp, $query, $rule);

        // 验证 Mockery 期望并计入 PHPUnit 断言计数
        Mockery::close();
        $this->addToAssertionCount(1);
    }

    public function test_apply_equal_condition(): void
    {
        $builder = Mockery::mock(Builder::class);
        $builder->shouldReceive('where')->with('status', '=', 'active')->once();

        $rule = new DataRule([
            'field' => 'status',
            'condition' => DataRule::CONDITION_EQUAL,
            'value' => 'active',
            'value_type' => DataRule::VALUE_TYPE_FIXED,
            'scope' => DataRule::SCOPE_ROW,
        ]);

        $this->callApplyCondition($builder, $rule);
    }

    public function test_apply_not_equal_condition(): void
    {
        $builder = Mockery::mock(Builder::class);
        $builder->shouldReceive('where')->with('status', '!=', 'deleted')->once();

        $rule = new DataRule([
            'field' => 'status',
            'condition' => DataRule::CONDITION_NOT_EQUAL,
            'value' => 'deleted',
            'value_type' => DataRule::VALUE_TYPE_FIXED,
            'scope' => DataRule::SCOPE_ROW,
        ]);

        $this->callApplyCondition($builder, $rule);
    }

    public function test_apply_greater_condition(): void
    {
        $builder = Mockery::mock(Builder::class);
        $builder->shouldReceive('where')->with('age', '>', '18')->once();

        $rule = new DataRule([
            'field' => 'age',
            'condition' => DataRule::CONDITION_GREATER,
            'value' => '18',
            'value_type' => DataRule::VALUE_TYPE_FIXED,
            'scope' => DataRule::SCOPE_ROW,
        ]);

        $this->callApplyCondition($builder, $rule);
    }

    public function test_apply_greater_equal_condition(): void
    {
        $builder = Mockery::mock(Builder::class);
        $builder->shouldReceive('where')->with('age', '>=', '18')->once();

        $rule = new DataRule([
            'field' => 'age',
            'condition' => DataRule::CONDITION_GREATER_EQUAL,
            'value' => '18',
            'value_type' => DataRule::VALUE_TYPE_FIXED,
            'scope' => DataRule::SCOPE_ROW,
        ]);

        $this->callApplyCondition($builder, $rule);
    }

    public function test_apply_less_condition(): void
    {
        $builder = Mockery::mock(Builder::class);
        $builder->shouldReceive('where')->with('price', '<', '100')->once();

        $rule = new DataRule([
            'field' => 'price',
            'condition' => DataRule::CONDITION_LESS,
            'value' => '100',
            'value_type' => DataRule::VALUE_TYPE_FIXED,
            'scope' => DataRule::SCOPE_ROW,
        ]);

        $this->callApplyCondition($builder, $rule);
    }

    public function test_apply_less_equal_condition(): void
    {
        $builder = Mockery::mock(Builder::class);
        $builder->shouldReceive('where')->with('price', '<=', '100')->once();

        $rule = new DataRule([
            'field' => 'price',
            'condition' => DataRule::CONDITION_LESS_EQUAL,
            'value' => '100',
            'value_type' => DataRule::VALUE_TYPE_FIXED,
            'scope' => DataRule::SCOPE_ROW,
        ]);

        $this->callApplyCondition($builder, $rule);
    }

    public function test_apply_like_condition(): void
    {
        $builder = Mockery::mock(Builder::class);
        $builder->shouldReceive('where')->with('name', 'like', '%test%')->once();

        $rule = new DataRule([
            'field' => 'name',
            'condition' => DataRule::CONDITION_LIKE,
            'value' => 'test',
            'value_type' => DataRule::VALUE_TYPE_FIXED,
            'scope' => DataRule::SCOPE_ROW,
        ]);

        $this->callApplyCondition($builder, $rule);
    }

    public function test_apply_in_condition(): void
    {
        $builder = Mockery::mock(Builder::class);
        $builder->shouldReceive('whereIn')->with('status', ['active', 'pending'])->once();

        $rule = new DataRule([
            'field' => 'status',
            'condition' => DataRule::CONDITION_IN,
            'value' => 'active,pending',
            'value_type' => DataRule::VALUE_TYPE_FIXED,
            'scope' => DataRule::SCOPE_ROW,
        ]);

        $this->callApplyCondition($builder, $rule);
    }

    public function test_apply_not_in_condition(): void
    {
        $builder = Mockery::mock(Builder::class);
        $builder->shouldReceive('whereNotIn')->with('status', ['deleted', 'archived'])->once();

        $rule = new DataRule([
            'field' => 'status',
            'condition' => DataRule::CONDITION_NOT_IN,
            'value' => 'deleted,archived',
            'value_type' => DataRule::VALUE_TYPE_FIXED,
            'scope' => DataRule::SCOPE_ROW,
        ]);

        $this->callApplyCondition($builder, $rule);
    }

    public function test_apply_between_condition(): void
    {
        $builder = Mockery::mock(Builder::class);
        $builder->shouldReceive('whereBetween')->with('age', ['18', '65'])->once();

        $rule = new DataRule([
            'field' => 'age',
            'condition' => DataRule::CONDITION_BETWEEN,
            'value' => '18,65',
            'value_type' => DataRule::VALUE_TYPE_FIXED,
            'scope' => DataRule::SCOPE_ROW,
        ]);

        $this->callApplyCondition($builder, $rule);
    }

    public function test_rejects_invalid_field_name_with_sql_injection(): void
    {
        $builder = Mockery::mock(Builder::class);
        // 不应有任何查询调用
        $builder->shouldNotReceive('where');
        $builder->shouldNotReceive('whereIn');

        $rule = new DataRule([
            'field' => 'id; DROP TABLE users --',
            'condition' => DataRule::CONDITION_EQUAL,
            'value' => '1',
            'value_type' => DataRule::VALUE_TYPE_FIXED,
            'scope' => DataRule::SCOPE_ROW,
        ]);

        $this->callApplyCondition($builder, $rule);
    }

    public function test_rejects_field_name_with_special_characters(): void
    {
        $builder = Mockery::mock(Builder::class);
        $builder->shouldNotReceive('where');

        $rule = new DataRule([
            'field' => 'field`name',
            'condition' => DataRule::CONDITION_EQUAL,
            'value' => '1',
            'value_type' => DataRule::VALUE_TYPE_FIXED,
            'scope' => DataRule::SCOPE_ROW,
        ]);

        $this->callApplyCondition($builder, $rule);
    }

    public function test_rejects_field_name_with_parentheses(): void
    {
        $builder = Mockery::mock(Builder::class);
        $builder->shouldNotReceive('where');

        $rule = new DataRule([
            'field' => 'COUNT(*)',
            'condition' => DataRule::CONDITION_EQUAL,
            'value' => '1',
            'value_type' => DataRule::VALUE_TYPE_FIXED,
            'scope' => DataRule::SCOPE_ROW,
        ]);

        $this->callApplyCondition($builder, $rule);
    }

    public function test_accepts_valid_dotted_field_name(): void
    {
        $builder = Mockery::mock(Builder::class);
        $builder->shouldReceive('where')->with('table.column', '=', 'value')->once();

        $rule = new DataRule([
            'field' => 'table.column',
            'condition' => DataRule::CONDITION_EQUAL,
            'value' => 'value',
            'value_type' => DataRule::VALUE_TYPE_FIXED,
            'scope' => DataRule::SCOPE_ROW,
        ]);

        $this->callApplyCondition($builder, $rule);
    }

    public function test_rejects_unknown_condition(): void
    {
        $builder = Mockery::mock(Builder::class);
        $builder->shouldNotReceive('where');
        $builder->shouldNotReceive('whereIn');
        $builder->shouldNotReceive('whereNotIn');
        $builder->shouldNotReceive('whereBetween');

        $rule = new DataRule([
            'field' => 'status',
            'condition' => 'invalid_condition',
            'value' => '1',
            'value_type' => DataRule::VALUE_TYPE_FIXED,
            'scope' => DataRule::SCOPE_ROW,
        ]);

        $this->callApplyCondition($builder, $rule);
    }

    public function test_resolve_fixed_value(): void
    {
        $dp = new DataPermission(null);

        $rule = new DataRule([
            'value' => 'test_value',
            'value_type' => DataRule::VALUE_TYPE_FIXED,
        ]);

        $this->assertSame('test_value', $dp->resolveValue($rule));
    }

    public function test_resolve_variable_value_without_user(): void
    {
        $dp = new DataPermission(null);

        $rule = new DataRule([
            'value' => '{user_id}',
            'value_type' => DataRule::VALUE_TYPE_VARIABLE,
        ]);

        // 没有用户时，变量不会被替换
        $this->assertSame('{user_id}', $dp->resolveValue($rule));
    }

    public function test_between_with_insufficient_values(): void
    {
        $builder = Mockery::mock(Builder::class);
        // between 需要至少 2 个值，只有 1 个时不应调用 whereBetween
        $builder->shouldNotReceive('whereBetween');

        $rule = new DataRule([
            'field' => 'age',
            'condition' => DataRule::CONDITION_BETWEEN,
            'value' => 'only_one',
            'value_type' => DataRule::VALUE_TYPE_FIXED,
            'scope' => DataRule::SCOPE_ROW,
        ]);

        $this->callApplyCondition($builder, $rule);
    }

    public function test_in_condition_with_array_value(): void
    {
        $builder = Mockery::mock(Builder::class);
        $builder->shouldReceive('whereIn')->with('id', [1, 2, 3])->once();

        $dp = new DataPermission(null);
        $reflection = new \ReflectionMethod($dp, 'applyCondition');
        $reflection->setAccessible(true);

        $rule = new DataRule([
            'field' => 'id',
            'condition' => DataRule::CONDITION_IN,
            'value' => [1, 2, 3],
            'value_type' => DataRule::VALUE_TYPE_FIXED,
            'scope' => DataRule::SCOPE_ROW,
        ]);

        // value 直接是数组时，也需要正确处理
        $reflection->invoke($dp, $builder, $rule);

        Mockery::close();
        $this->addToAssertionCount(1);
    }
}
