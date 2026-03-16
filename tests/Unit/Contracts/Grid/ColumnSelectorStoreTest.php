<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Contracts\Grid;

use Dcat\Admin\Contracts\Grid\ColumnSelectorStore;
use Dcat\Admin\Grid;
use Dcat\Admin\Tests\TestCase;
use Mockery;

class ColumnSelectorStoreTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_interface_exists(): void
    {
        $this->assertTrue(interface_exists(ColumnSelectorStore::class));
    }

    public function test_anonymous_class_implements_interface(): void
    {
        $instance = $this->makeColumnSelectorStore();

        $this->assertInstanceOf(ColumnSelectorStore::class, $instance);
    }

    public function test_set_grid_method_is_defined(): void
    {
        $reflection = new \ReflectionClass(ColumnSelectorStore::class);

        $this->assertTrue($reflection->hasMethod('setGrid'));
    }

    public function test_store_method_is_defined(): void
    {
        $reflection = new \ReflectionClass(ColumnSelectorStore::class);

        $this->assertTrue($reflection->hasMethod('store'));
    }

    public function test_get_method_is_defined(): void
    {
        $reflection = new \ReflectionClass(ColumnSelectorStore::class);

        $this->assertTrue($reflection->hasMethod('get'));
    }

    public function test_forget_method_is_defined(): void
    {
        $reflection = new \ReflectionClass(ColumnSelectorStore::class);

        $this->assertTrue($reflection->hasMethod('forget'));
    }

    public function test_interface_has_exactly_four_methods(): void
    {
        $reflection = new \ReflectionClass(ColumnSelectorStore::class);

        $this->assertCount(4, $reflection->getMethods());
    }

    public function test_set_grid_accepts_grid_parameter(): void
    {
        $reflection = new \ReflectionMethod(ColumnSelectorStore::class, 'setGrid');
        $params = $reflection->getParameters();

        $this->assertCount(1, $params);
        $this->assertSame('grid', $params[0]->getName());
        $this->assertSame(Grid::class, $params[0]->getType()->getName());
    }

    public function test_store_accepts_array_parameter(): void
    {
        $reflection = new \ReflectionMethod(ColumnSelectorStore::class, 'store');
        $params = $reflection->getParameters();

        $this->assertCount(1, $params);
        $this->assertSame('input', $params[0]->getName());
        $this->assertSame('array', $params[0]->getType()->getName());
    }

    public function test_get_has_no_parameters(): void
    {
        $reflection = new \ReflectionMethod(ColumnSelectorStore::class, 'get');

        $this->assertCount(0, $reflection->getParameters());
    }

    public function test_forget_has_no_parameters(): void
    {
        $reflection = new \ReflectionMethod(ColumnSelectorStore::class, 'forget');

        $this->assertCount(0, $reflection->getParameters());
    }

    public function test_store_and_get_round_trip(): void
    {
        $instance = $this->makeColumnSelectorStore();
        $data = ['column_a', 'column_b', 'column_c'];

        $instance->store($data);
        $result = $instance->get();

        $this->assertSame($data, $result);
    }

    public function test_get_returns_null_when_empty(): void
    {
        $instance = $this->makeColumnSelectorStore();

        $this->assertNull($instance->get());
    }

    public function test_forget_clears_stored_data(): void
    {
        $instance = $this->makeColumnSelectorStore();
        $instance->store(['col1', 'col2']);

        $instance->forget();

        $this->assertNull($instance->get());
    }

    protected function makeColumnSelectorStore(): ColumnSelectorStore
    {
        return new class implements ColumnSelectorStore
        {
            protected ?array $data = null;

            public function setGrid(Grid $grid)
            {
                return $this;
            }

            public function store(array $input)
            {
                $this->data = $input;
            }

            public function get()
            {
                return $this->data;
            }

            public function forget()
            {
                $this->data = null;
            }
        };
    }
}
