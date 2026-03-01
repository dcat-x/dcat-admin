<?php

namespace Dcat\Admin\Tests\Unit\Show;

use Dcat\Admin\Show\Field;
use Dcat\Admin\Show\Relation;
use Dcat\Admin\Tests\TestCase;
use Illuminate\Support\Fluent;

class RelationTest extends TestCase
{
    public function test_constructor_sets_properties(): void
    {
        $builder = function () {
            return 'test';
        };
        $relation = new Relation('comments', $builder, 'Comments');

        $this->assertInstanceOf(Relation::class, $relation);
        $this->assertInstanceOf(Field::class, $relation);
    }

    public function test_default_width(): void
    {
        $relation = new Relation('comments', function () {}, 'Comments');
        $this->assertEquals(12, $relation->width);
    }

    public function test_width_can_be_set(): void
    {
        $relation = new Relation('comments', function () {}, 'Comments');
        $result = $relation->width(6);

        $this->assertSame($relation, $result);
        $this->assertEquals(6, $relation->width);
    }

    public function test_model_getter_and_setter(): void
    {
        $relation = new Relation('comments', function () {}, 'Comments');
        $model = new Fluent(['id' => 1, 'name' => 'Test']);

        $result = $relation->model($model);
        $this->assertSame($relation, $result);
        $this->assertSame($model, $relation->model());
    }

    public function test_model_returns_null_by_default(): void
    {
        $relation = new Relation('comments', function () {}, 'Comments');
        $this->assertNull($relation->model());
    }
}
