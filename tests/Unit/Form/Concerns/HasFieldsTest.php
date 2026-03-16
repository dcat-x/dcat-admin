<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Form\Concerns;

use Dcat\Admin\Form\Concerns\HasFields;
use Dcat\Admin\Form\Field;
use Dcat\Admin\Tests\TestCase;
use Illuminate\Support\Collection;
use Mockery;

class HasFieldsTestHelper
{
    use HasFields;
}

class HasFieldsTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    protected function createHelper(): HasFieldsTestHelper
    {
        return new HasFieldsTestHelper;
    }

    protected function createMockField(string $column): Field
    {
        $field = Mockery::mock(Field::class);
        $field->shouldReceive('column')->andReturn($column);

        return $field;
    }

    public function test_fields_lazy_creates_collection(): void
    {
        $helper = $this->createHelper();

        $fields = $helper->fields();

        $this->assertInstanceOf(Collection::class, $fields);
    }

    public function test_fields_returns_collection(): void
    {
        $helper = $this->createHelper();

        $this->assertInstanceOf(Collection::class, $helper->fields());
    }

    public function test_push_field_adds_to_collection(): void
    {
        $helper = $this->createHelper();
        $field = $this->createMockField('name');

        $helper->pushField($field);

        $this->assertCount(1, $helper->fields());
    }

    public function test_remove_field_removes_by_column(): void
    {
        $helper = $this->createHelper();
        $field1 = $this->createMockField('name');
        $field2 = $this->createMockField('email');

        $helper->pushField($field1);
        $helper->pushField($field2);
        $helper->removeField('name');

        $this->assertCount(1, $helper->fields());
        $this->assertSame($field2, $helper->fields()->first());
    }

    public function test_reset_fields_clears_collection(): void
    {
        $helper = $this->createHelper();
        $helper->pushField($this->createMockField('name'));
        $helper->pushField($this->createMockField('email'));

        $helper->resetFields();

        $this->assertCount(0, $helper->fields());
    }

    public function test_reject_fields_filters_collection(): void
    {
        $helper = $this->createHelper();
        $field1 = $this->createMockField('name');
        $field2 = $this->createMockField('email');

        $helper->pushField($field1);
        $helper->pushField($field2);

        $helper->rejectFields(function (Field $field) {
            return $field->column() === 'name';
        });

        $this->assertCount(1, $helper->fields());
    }

    public function test_set_fields_replaces_collection(): void
    {
        $helper = $this->createHelper();
        $helper->pushField($this->createMockField('name'));

        $newFields = new Collection([$this->createMockField('title'), $this->createMockField('body')]);
        $helper->setFields($newFields);

        $this->assertCount(2, $helper->fields());
    }

    public function test_field_finds_by_column_name(): void
    {
        $helper = $this->createHelper();
        $field = $this->createMockField('email');

        $helper->pushField($field);

        $found = $helper->field('email');
        $this->assertSame($field, $found);
    }

    public function test_field_returns_null_when_not_found(): void
    {
        $helper = $this->createHelper();
        $helper->pushField($this->createMockField('name'));

        $result = $helper->field('nonexistent');
        $this->assertNull($result);
    }

    public function test_fields_returns_same_instance_on_multiple_calls(): void
    {
        $helper = $this->createHelper();

        $first = $helper->fields();
        $second = $helper->fields();

        $this->assertSame($first, $second);
    }

    public function test_push_field_increments_count(): void
    {
        $helper = $this->createHelper();

        $helper->pushField($this->createMockField('a'));
        $this->assertCount(1, $helper->fields());

        $helper->pushField($this->createMockField('b'));
        $this->assertCount(2, $helper->fields());

        $helper->pushField($this->createMockField('c'));
        $this->assertCount(3, $helper->fields());
    }

    public function test_merged_fields_returns_array(): void
    {
        $helper = $this->createHelper();
        $field = $this->createMockField('name');
        $helper->pushField($field);

        $ref = new \ReflectionMethod($helper, 'mergedFields');
        $ref->setAccessible(true);
        $result = $ref->invoke($helper);

        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        $this->assertSame($field, $result[0]);
    }
}
