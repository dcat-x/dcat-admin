<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Grid\Column;

use Dcat\Admin\Grid\Column;
use Dcat\Admin\Grid\Column\Condition;
use Dcat\Admin\Tests\TestCase;
use Illuminate\Support\Fluent;
use Mockery;

class ConditionTest extends TestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }

    protected function createColumn(): Column
    {
        $column = Mockery::mock(Column::class)->makePartial();
        $column->shouldReceive('getDisplayCallbacks')->andReturn([]);
        $column->shouldReceive('setDisplayCallbacks')->andReturnNull();
        $column->shouldReceive('getOriginalModel')->andReturn(new Fluent);
        $column->shouldReceive('getOriginal')->andReturn(null);
        $column->shouldReceive('getValue')->andReturn(null);
        $column->shouldReceive('setOriginalModel')->andReturnNull();
        $column->shouldReceive('setOriginal')->andReturnNull();
        $column->shouldReceive('setValue')->andReturnNull();

        return $column;
    }

    public function test_constructor_clones_column(): void
    {
        $column = $this->createColumn();
        $condition = new Condition(true, $column);

        $ref = new \ReflectionProperty(Condition::class, 'original');
        $ref->setAccessible(true);
        $original = $ref->getValue($condition);

        $this->assertNotSame($column, $original);
    }

    public function test_constructor_stores_condition(): void
    {
        $column = $this->createColumn();
        $condition = new Condition('test_value', $column);

        $ref = new \ReflectionProperty(Condition::class, 'condition');
        $ref->setAccessible(true);
        $this->assertSame('test_value', $ref->getValue($condition));
    }

    public function test_then_stores_closure(): void
    {
        $column = $this->createColumn();
        $condition = new Condition(true, $column);

        $closure = function () {
            return 'test';
        };
        $condition->then($closure);

        $ref = new \ReflectionProperty(Condition::class, 'next');
        $ref->setAccessible(true);
        $this->assertCount(1, $ref->getValue($condition));
    }

    public function test_then_returns_self(): void
    {
        $column = $this->createColumn();
        $condition = new Condition(true, $column);

        $result = $condition->then(function () {});

        $this->assertSame($condition, $result);
    }

    public function test_is_evaluates_bool_true(): void
    {
        $column = $this->createColumn();
        $condition = new Condition(true, $column);

        $this->assertTrue($condition->is());
    }

    public function test_is_evaluates_bool_false(): void
    {
        $column = $this->createColumn();
        $condition = new Condition(false, $column);

        $this->assertFalse($condition->is());
    }

    public function test_is_evaluates_closure(): void
    {
        $column = $this->createColumn();
        $condition = new Condition(function () {
            return true;
        }, $column);

        $this->assertTrue($condition->is());
    }

    public function test_is_casts_to_bool(): void
    {
        $column = $this->createColumn();

        $condition1 = new Condition(1, $column);
        $this->assertTrue($condition1->is());

        $condition2 = new Condition(0, $column);
        $this->assertFalse($condition2->is());

        $condition3 = new Condition('non-empty', $column);
        $this->assertTrue($condition3->is());

        $condition4 = new Condition('', $column);
        $this->assertFalse($condition4->is());
    }

    public function test_get_result_returns_null_initially(): void
    {
        $column = $this->createColumn();
        $condition = new Condition(true, $column);

        $this->assertNull($condition->getResult());
    }

    public function test_get_result_returns_result_after_is(): void
    {
        $column = $this->createColumn();
        $condition = new Condition(true, $column);

        $condition->is();

        $this->assertTrue($condition->getResult());
    }

    public function test_reset_restores_original_displayers(): void
    {
        $callbacks = [function () {
            return 'original';
        }];

        $column = Mockery::mock(Column::class)->makePartial();
        $column->shouldReceive('getDisplayCallbacks')->andReturn($callbacks);
        $column->shouldReceive('setDisplayCallbacks')->once()->with($callbacks);
        $column->shouldReceive('getOriginalModel')->andReturn(new Fluent);

        $condition = new Condition(true, $column);
        $condition->reset();
    }

    public function test_call_proxies_to_column_for_if(): void
    {
        $column = Mockery::mock(Column::class)->makePartial();
        $column->shouldReceive('getDisplayCallbacks')->andReturn([]);
        $column->shouldReceive('getOriginalModel')->andReturn(new Fluent);

        $mockCondition = Mockery::mock(Condition::class);
        $column->shouldReceive('if')->once()->andReturn($mockCondition);

        $condition = new Condition(true, $column);
        $result = $condition->if(function () {
            return true;
        });

        $this->assertSame($mockCondition, $result);
    }
}
