<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Form;

use Dcat\Admin\Form\NestedForm;
use Dcat\Admin\Tests\TestCase;
use Dcat\Admin\Widgets\Form as WidgetForm;
use Illuminate\Support\Collection;
use Mockery;

class NestedFormTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_constants(): void
    {
        $this->assertSame('new_', NestedForm::DEFAULT_KEY_PREFIX);
        $this->assertSame('__PARENT_NESTED__', NestedForm::DEFAULT_PARENT_KEY_NAME);
        $this->assertSame('__NESTED__', NestedForm::DEFAULT_KEY_NAME);
        $this->assertSame('_remove_', NestedForm::REMOVE_FLAG_NAME);
        $this->assertSame('form-removed', NestedForm::REMOVE_FLAG_CLASS);
    }

    public function test_constructor_sets_relation_and_key(): void
    {
        $nestedForm = new NestedForm('items', 1);

        $refRelation = new \ReflectionProperty($nestedForm, 'relationName');
        $refRelation->setAccessible(true);
        $this->assertSame('items', $refRelation->getValue($nestedForm));

        $refKey = new \ReflectionProperty($nestedForm, 'key');
        $refKey->setAccessible(true);
        $this->assertSame(1, $refKey->getValue($nestedForm));
    }

    public function test_constructor_with_null_values(): void
    {
        $nestedForm = new NestedForm;

        $refRelation = new \ReflectionProperty($nestedForm, 'relationName');
        $refRelation->setAccessible(true);
        $this->assertNull($refRelation->getValue($nestedForm));

        $refKey = new \ReflectionProperty($nestedForm, 'key');
        $refKey->setAccessible(true);
        $this->assertNull($refKey->getValue($nestedForm));
    }

    public function test_set_form_returns_this(): void
    {
        $nestedForm = new NestedForm('items', 1);
        $mockForm = Mockery::mock(WidgetForm::class);

        $result = $nestedForm->setForm($mockForm);
        $this->assertSame($nestedForm, $result);
    }

    public function test_form_returns_set_form(): void
    {
        $nestedForm = new NestedForm('items', 1);
        $mockForm = Mockery::mock(WidgetForm::class);

        $nestedForm->setForm($mockForm);
        $this->assertSame($mockForm, $nestedForm->form());
    }

    public function test_get_key_returns_constructor_key(): void
    {
        $nestedForm = new NestedForm('items', 5);

        $this->assertSame(5, $nestedForm->getKey());
    }

    public function test_set_key_returns_this(): void
    {
        $nestedForm = new NestedForm('items', 1);

        $result = $nestedForm->setKey(10);
        $this->assertSame($nestedForm, $result);
    }

    public function test_set_key_changes_key(): void
    {
        $nestedForm = new NestedForm('items', 1);

        $nestedForm->setKey(99);
        $this->assertSame(99, $nestedForm->getKey());
    }

    public function test_fields_returns_collection(): void
    {
        $nestedForm = new NestedForm('items', 1);

        $this->assertInstanceOf(Collection::class, $nestedForm->fields());
    }

    public function test_fill_returns_this(): void
    {
        $nestedForm = new NestedForm('items', 1);

        $result = $nestedForm->fill([]);
        $this->assertSame($nestedForm, $result);
    }

    public function test_get_default_key(): void
    {
        $nestedForm = new NestedForm('items', 1);

        $expected = NestedForm::DEFAULT_KEY_PREFIX.NestedForm::DEFAULT_KEY_NAME;
        $this->assertSame('new___NESTED__', $nestedForm->getDefaultKey());
        $this->assertSame($expected, $nestedForm->getDefaultKey());
    }

    public function test_set_default_key(): void
    {
        $nestedForm = new NestedForm('items', 1);

        $result = $nestedForm->setDefaultKey('custom_key');
        $this->assertSame($nestedForm, $result);
        $this->assertSame('custom_key', $nestedForm->getDefaultKey());
    }

    public function test_set_original_with_empty_data_returns_this(): void
    {
        $nestedForm = new NestedForm('items', 1);

        $result = $nestedForm->setOriginal([], 'id');
        $this->assertSame($nestedForm, $result);

        $ref = new \ReflectionProperty($nestedForm, 'original');
        $ref->setAccessible(true);
        $this->assertSame([], $ref->getValue($nestedForm));
    }

    public function test_set_original_stores_keyed_data(): void
    {
        $nestedForm = new NestedForm('items', 1);

        $data = [
            ['id' => 10, 'name' => 'Item A'],
            ['id' => 20, 'name' => 'Item B'],
        ];

        $result = $nestedForm->setOriginal($data, 'id');
        $this->assertSame($nestedForm, $result);

        $ref = new \ReflectionProperty($nestedForm, 'original');
        $ref->setAccessible(true);
        $original = $ref->getValue($nestedForm);

        $this->assertContains(10, array_keys($original));
        $this->assertContains(20, array_keys($original));
        $this->assertSame(['id' => 10, 'name' => 'Item A'], $original[10]);
        $this->assertSame(['id' => 20, 'name' => 'Item B'], $original[20]);
    }
}
