<?php

namespace Dcat\Admin\Tests\Unit\Grid\Filter\Presenter;

use Dcat\Admin\Grid\Filter\Presenter\BatchInput;
use Dcat\Admin\Grid\Filter\Presenter\Presenter;
use Dcat\Admin\Tests\TestCase;
use ReflectionProperty;

class BatchInputTest extends TestCase
{
    protected function makeBatchInput(string $lookupUrl = '/api/lookup'): BatchInput
    {
        return new BatchInput($lookupUrl);
    }

    protected function getProtectedProperty(object $object, string $property)
    {
        $reflection = new ReflectionProperty($object, $property);
        $reflection->setAccessible(true);

        return $reflection->getValue($object);
    }

    // ===== Class structure =====

    public function test_extends_presenter(): void
    {
        $parents = class_parents(BatchInput::class);

        $this->assertContains(Presenter::class, $parents);
    }

    public function test_has_css_property_with_select2(): void
    {
        $ref = new ReflectionProperty(BatchInput::class, 'css');
        $ref->setAccessible(true);

        $this->assertContains('@select2', $ref->getDefaultValue());
    }

    public function test_view_returns_default_view_name(): void
    {
        $batch = $this->makeBatchInput();

        $this->assertEquals('admin::filter.batchinput', $batch->view());
    }

    // ===== Constructor =====

    public function test_constructor_sets_lookup_url(): void
    {
        $batch = $this->makeBatchInput('/api/custom-lookup');

        $this->assertEquals('/api/custom-lookup', $this->getProtectedProperty($batch, 'lookupUrl'));
    }

    public function test_constructor_generates_id(): void
    {
        $batch = $this->makeBatchInput();
        $id = $this->getProtectedProperty($batch, 'id');

        $this->assertStringStartsWith('batch-input-', $id);
        $this->assertEquals(20, strlen($id)); // 'batch-input-' (12) + random(8)
    }

    public function test_constructor_generates_unique_ids(): void
    {
        $batch1 = $this->makeBatchInput();
        $batch2 = $this->makeBatchInput();

        $this->assertNotEquals(
            $this->getProtectedProperty($batch1, 'id'),
            $this->getProtectedProperty($batch2, 'id')
        );
    }

    // ===== Default values =====

    public function test_default_placeholder_is_empty(): void
    {
        $batch = $this->makeBatchInput();

        $this->assertEquals('', $this->getProtectedProperty($batch, 'placeholder'));
    }

    public function test_default_batch_title_is_empty(): void
    {
        $batch = $this->makeBatchInput();

        $this->assertEquals('', $this->getProtectedProperty($batch, 'batchTitle'));
    }

    public function test_default_batch_description_is_empty(): void
    {
        $batch = $this->makeBatchInput();

        $this->assertEquals('', $this->getProtectedProperty($batch, 'batchDescription'));
    }

    public function test_default_batch_icon(): void
    {
        $batch = $this->makeBatchInput();

        $this->assertEquals('feather icon-list', $this->getProtectedProperty($batch, 'batchIcon'));
    }

    public function test_default_batch_button_text_is_empty(): void
    {
        $batch = $this->makeBatchInput();

        $this->assertEquals('', $this->getProtectedProperty($batch, 'batchButtonText'));
    }

    public function test_default_batch_max_is_100(): void
    {
        $batch = $this->makeBatchInput();

        $this->assertEquals(100, $this->getProtectedProperty($batch, 'batchMax'));
    }

    public function test_default_validation_pattern_is_empty(): void
    {
        $batch = $this->makeBatchInput();

        $this->assertEquals('', $this->getProtectedProperty($batch, 'validationPattern'));
    }

    public function test_default_validation_message_is_empty(): void
    {
        $batch = $this->makeBatchInput();

        $this->assertEquals('', $this->getProtectedProperty($batch, 'validationMessage'));
    }

    public function test_default_item_label_is_empty(): void
    {
        $batch = $this->makeBatchInput();

        $this->assertEquals('', $this->getProtectedProperty($batch, 'itemLabel'));
    }

    public function test_default_batch_placeholder_is_empty(): void
    {
        $batch = $this->makeBatchInput();

        $this->assertEquals('', $this->getProtectedProperty($batch, 'batchPlaceholder'));
    }

    public function test_default_query_field_is_keywords(): void
    {
        $batch = $this->makeBatchInput();

        $this->assertEquals('keywords', $this->getProtectedProperty($batch, 'queryField'));
    }

    public function test_default_model_is_null(): void
    {
        $batch = $this->makeBatchInput();

        $this->assertNull($this->getProtectedProperty($batch, 'model'));
    }

    public function test_default_model_key_is_id(): void
    {
        $batch = $this->makeBatchInput();

        $this->assertEquals('id', $this->getProtectedProperty($batch, 'modelKey'));
    }

    public function test_default_model_text_is_name(): void
    {
        $batch = $this->makeBatchInput();

        $this->assertEquals('name', $this->getProtectedProperty($batch, 'modelText'));
    }

    // ===== Setter fluent interface =====

    public function test_placeholder_is_fluent(): void
    {
        $batch = $this->makeBatchInput();

        $this->assertSame($batch, $batch->placeholder('Search...'));
    }

    public function test_placeholder_sets_value(): void
    {
        $batch = $this->makeBatchInput();

        $batch->placeholder('Type here...');

        $this->assertEquals('Type here...', $this->getProtectedProperty($batch, 'placeholder'));
    }

    public function test_batch_title_is_fluent(): void
    {
        $batch = $this->makeBatchInput();

        $this->assertSame($batch, $batch->batchTitle('Custom Title'));
    }

    public function test_batch_title_sets_value(): void
    {
        $batch = $this->makeBatchInput();

        $batch->batchTitle('Custom Title');

        $this->assertEquals('Custom Title', $this->getProtectedProperty($batch, 'batchTitle'));
    }

    public function test_batch_description_is_fluent(): void
    {
        $batch = $this->makeBatchInput();

        $this->assertSame($batch, $batch->batchDescription('Custom Desc'));
    }

    public function test_batch_description_sets_value(): void
    {
        $batch = $this->makeBatchInput();

        $batch->batchDescription('Custom Description');

        $this->assertEquals('Custom Description', $this->getProtectedProperty($batch, 'batchDescription'));
    }

    public function test_batch_icon_is_fluent(): void
    {
        $batch = $this->makeBatchInput();

        $this->assertSame($batch, $batch->batchIcon('fa fa-search'));
    }

    public function test_batch_icon_sets_value(): void
    {
        $batch = $this->makeBatchInput();

        $batch->batchIcon('fa fa-search');

        $this->assertEquals('fa fa-search', $this->getProtectedProperty($batch, 'batchIcon'));
    }

    public function test_batch_button_text_is_fluent(): void
    {
        $batch = $this->makeBatchInput();

        $this->assertSame($batch, $batch->batchButtonText('Import'));
    }

    public function test_batch_button_text_sets_value(): void
    {
        $batch = $this->makeBatchInput();

        $batch->batchButtonText('Import');

        $this->assertEquals('Import', $this->getProtectedProperty($batch, 'batchButtonText'));
    }

    public function test_batch_max_is_fluent(): void
    {
        $batch = $this->makeBatchInput();

        $this->assertSame($batch, $batch->batchMax(50));
    }

    public function test_batch_max_sets_value(): void
    {
        $batch = $this->makeBatchInput();

        $batch->batchMax(200);

        $this->assertEquals(200, $this->getProtectedProperty($batch, 'batchMax'));
    }

    public function test_validation_pattern_is_fluent(): void
    {
        $batch = $this->makeBatchInput();

        $this->assertSame($batch, $batch->validationPattern('^\d+$'));
    }

    public function test_validation_pattern_sets_pattern(): void
    {
        $batch = $this->makeBatchInput();

        $batch->validationPattern('^\d{6}$');

        $this->assertEquals('^\d{6}$', $this->getProtectedProperty($batch, 'validationPattern'));
    }

    public function test_validation_pattern_sets_message(): void
    {
        $batch = $this->makeBatchInput();

        $batch->validationPattern('^\d+$', 'Must be numeric');

        $this->assertEquals('Must be numeric', $this->getProtectedProperty($batch, 'validationMessage'));
    }

    public function test_validation_pattern_without_message_keeps_empty(): void
    {
        $batch = $this->makeBatchInput();

        $batch->validationPattern('^\d+$');

        $this->assertEquals('', $this->getProtectedProperty($batch, 'validationMessage'));
    }

    public function test_item_label_is_fluent(): void
    {
        $batch = $this->makeBatchInput();

        $this->assertSame($batch, $batch->itemLabel('email'));
    }

    public function test_item_label_sets_value(): void
    {
        $batch = $this->makeBatchInput();

        $batch->itemLabel('phone');

        $this->assertEquals('phone', $this->getProtectedProperty($batch, 'itemLabel'));
    }

    public function test_batch_placeholder_is_fluent(): void
    {
        $batch = $this->makeBatchInput();

        $this->assertSame($batch, $batch->batchPlaceholder('Enter emails...'));
    }

    public function test_batch_placeholder_sets_value(): void
    {
        $batch = $this->makeBatchInput();

        $batch->batchPlaceholder('Enter phone numbers...');

        $this->assertEquals('Enter phone numbers...', $this->getProtectedProperty($batch, 'batchPlaceholder'));
    }

    public function test_query_field_is_fluent(): void
    {
        $batch = $this->makeBatchInput();

        $this->assertSame($batch, $batch->queryField('ids'));
    }

    public function test_query_field_sets_value(): void
    {
        $batch = $this->makeBatchInput();

        $batch->queryField('emails');

        $this->assertEquals('emails', $this->getProtectedProperty($batch, 'queryField'));
    }

    public function test_model_is_fluent(): void
    {
        $batch = $this->makeBatchInput();

        $this->assertSame($batch, $batch->model('App\\Models\\User'));
    }

    public function test_model_sets_model_class(): void
    {
        $batch = $this->makeBatchInput();

        $batch->model('App\\Models\\User');

        $this->assertEquals('App\\Models\\User', $this->getProtectedProperty($batch, 'model'));
    }

    public function test_model_sets_key_and_text(): void
    {
        $batch = $this->makeBatchInput();

        $batch->model('App\\Models\\User', 'uid', 'username');

        $this->assertEquals('uid', $this->getProtectedProperty($batch, 'modelKey'));
        $this->assertEquals('username', $this->getProtectedProperty($batch, 'modelText'));
    }

    public function test_model_uses_default_key_and_text(): void
    {
        $batch = $this->makeBatchInput();

        $batch->model('App\\Models\\User');

        $this->assertEquals('id', $this->getProtectedProperty($batch, 'modelKey'));
        $this->assertEquals('name', $this->getProtectedProperty($batch, 'modelText'));
    }

    // ===== Fluent chaining =====

    public function test_method_chaining(): void
    {
        $batch = $this->makeBatchInput('/api/lookup');

        $result = $batch
            ->placeholder('Search...')
            ->batchTitle('Batch Lookup')
            ->batchDescription('Enter values')
            ->batchIcon('fa fa-search')
            ->batchButtonText('Batch')
            ->batchMax(50)
            ->validationPattern('^\d+$', 'Numbers only')
            ->itemLabel('ID')
            ->batchPlaceholder('Enter IDs here...')
            ->queryField('ids')
            ->model('App\\Models\\User', 'id', 'name');

        $this->assertSame($batch, $result);
        $this->assertEquals('Search...', $this->getProtectedProperty($batch, 'placeholder'));
        $this->assertEquals('Batch Lookup', $this->getProtectedProperty($batch, 'batchTitle'));
        $this->assertEquals(50, $this->getProtectedProperty($batch, 'batchMax'));
        $this->assertEquals('Enter IDs here...', $this->getProtectedProperty($batch, 'batchPlaceholder'));
        $this->assertEquals('ids', $this->getProtectedProperty($batch, 'queryField'));
    }

    // ===== Protected methods =====

    public function test_resolve_display_items_returns_empty_when_no_filter(): void
    {
        $batch = $this->makeBatchInput();

        $ref = new \ReflectionMethod($batch, 'resolveDisplayItems');
        $ref->setAccessible(true);

        // Without a parent filter, value() will fail, but model is null so it returns early
        $filter = $this->createMock(\Dcat\Admin\Grid\Filter\AbstractFilter::class);
        $filter->method('getValue')->willReturn(null);
        $filter->method('getDefault')->willReturn(null);
        $batch->setParent($filter);

        $this->assertEquals([], $ref->invoke($batch));
    }

    public function test_resolve_display_items_returns_empty_when_value_is_empty(): void
    {
        $batch = $this->makeBatchInput();

        $filter = $this->createMock(\Dcat\Admin\Grid\Filter\AbstractFilter::class);
        $filter->method('getValue')->willReturn('');
        $filter->method('getDefault')->willReturn(null);
        $batch->setParent($filter);

        $ref = new \ReflectionMethod($batch, 'resolveDisplayItems');
        $ref->setAccessible(true);

        $this->assertEquals([], $ref->invoke($batch));
    }

    public function test_resolve_display_items_returns_empty_when_no_model(): void
    {
        $batch = $this->makeBatchInput();

        $filter = $this->createMock(\Dcat\Admin\Grid\Filter\AbstractFilter::class);
        $filter->method('getValue')->willReturn('1,2,3');
        $filter->method('getDefault')->willReturn(null);
        $batch->setParent($filter);

        $ref = new \ReflectionMethod($batch, 'resolveDisplayItems');
        $ref->setAccessible(true);

        // model is null, so should return empty
        $this->assertEquals([], $ref->invoke($batch));
    }

    public function test_add_script_is_protected(): void
    {
        $ref = new \ReflectionMethod(BatchInput::class, 'addScript');

        $this->assertTrue($ref->isProtected());
    }

    public function test_resolve_display_items_is_protected(): void
    {
        $ref = new \ReflectionMethod(BatchInput::class, 'resolveDisplayItems');

        $this->assertTrue($ref->isProtected());
    }
}
