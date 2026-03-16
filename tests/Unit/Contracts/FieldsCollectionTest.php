<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Contracts;

use Dcat\Admin\Contracts\FieldsCollection;
use Dcat\Admin\Tests\TestCase;
use Illuminate\Support\Collection;
use Mockery;

class FieldsCollectionTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_interface_exists(): void
    {
        $this->assertTrue(interface_exists(FieldsCollection::class));
    }

    public function test_anonymous_class_implements_interface(): void
    {
        $instance = $this->makeFieldsCollection();

        $this->assertInstanceOf(FieldsCollection::class, $instance);
    }

    public function test_fields_method_is_defined(): void
    {
        $reflection = new \ReflectionClass(FieldsCollection::class);

        $this->assertTrue($reflection->hasMethod('fields'));
    }

    public function test_field_method_is_defined(): void
    {
        $reflection = new \ReflectionClass(FieldsCollection::class);

        $this->assertTrue($reflection->hasMethod('field'));
    }

    public function test_field_method_accepts_string_parameter(): void
    {
        $reflection = new \ReflectionMethod(FieldsCollection::class, 'field');
        $params = $reflection->getParameters();

        $this->assertCount(1, $params);
        $this->assertSame('name', $params[0]->getName());
    }

    public function test_fields_method_has_no_parameters(): void
    {
        $reflection = new \ReflectionMethod(FieldsCollection::class, 'fields');

        $this->assertCount(0, $reflection->getParameters());
    }

    public function test_fields_returns_collection(): void
    {
        $instance = $this->makeFieldsCollection();

        $result = $instance->fields();

        $this->assertInstanceOf(Collection::class, $result);
    }

    public function test_field_returns_null_when_not_found(): void
    {
        $instance = $this->makeFieldsCollection();

        $result = $instance->field('nonexistent');

        $this->assertNull($result);
    }

    public function test_interface_has_exactly_two_methods(): void
    {
        $reflection = new \ReflectionClass(FieldsCollection::class);

        $this->assertCount(2, $reflection->getMethods());
    }

    protected function makeFieldsCollection(): FieldsCollection
    {
        return new class implements FieldsCollection
        {
            protected Collection $items;

            public function __construct()
            {
                $this->items = new Collection;
            }

            public function fields()
            {
                return $this->items;
            }

            public function field($name)
            {
                return $this->items->first(function ($field) use ($name) {
                    return $field === $name;
                });
            }
        };
    }
}
