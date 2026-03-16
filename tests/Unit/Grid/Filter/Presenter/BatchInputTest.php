<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Grid\Filter\Presenter;

use Dcat\Admin\Grid\Filter\Presenter\BatchInput;
use Dcat\Admin\Grid\Filter\Presenter\Presenter;
use Dcat\Admin\Tests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
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

        $this->assertSame('admin::filter.batchinput', $batch->view());
    }

    // ===== Constructor =====

    public function test_constructor_sets_lookup_url(): void
    {
        $batch = $this->makeBatchInput('/api/custom-lookup');

        $this->assertSame('/api/custom-lookup', $this->getProtectedProperty($batch, 'lookupUrl'));
    }

    public function test_constructor_generates_id(): void
    {
        $batch = $this->makeBatchInput();
        $id = $this->getProtectedProperty($batch, 'id');

        $this->assertStringStartsWith('batch-input-', $id);
        $this->assertSame(20, strlen($id)); // 'batch-input-' (12) + random(8)
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

    #[DataProvider('defaultPropertyProvider')]
    public function test_default_property_values(string $property, mixed $expected): void
    {
        $batch = $this->makeBatchInput();

        $this->assertSame($expected, $this->getProtectedProperty($batch, $property));
    }

    public static function defaultPropertyProvider(): array
    {
        return [
            'placeholder' => ['placeholder', ''],
            'batchTitle' => ['batchTitle', ''],
            'batchDescription' => ['batchDescription', ''],
            'batchIcon' => ['batchIcon', 'feather icon-list'],
            'batchButtonText' => ['batchButtonText', ''],
            'batchMax' => ['batchMax', 100],
            'validationPattern' => ['validationPattern', ''],
            'validationMessage' => ['validationMessage', ''],
            'itemLabel' => ['itemLabel', ''],
            'batchPlaceholder' => ['batchPlaceholder', ''],
            'queryField' => ['queryField', 'keywords'],
            'model' => ['model', null],
            'modelKey' => ['modelKey', 'id'],
            'modelText' => ['modelText', 'name'],
        ];
    }

    // ===== Setter fluent interface =====

    #[DataProvider('fluentSetterProvider')]
    public function test_fluent_setter_updates_property(string $method, mixed $argument, string $property, mixed $expected): void
    {
        $batch = $this->makeBatchInput();

        $result = $batch->{$method}($argument);

        $this->assertSame($batch, $result);
        $this->assertSame($expected, $this->getProtectedProperty($batch, $property));
    }

    public static function fluentSetterProvider(): array
    {
        return [
            'placeholder' => ['placeholder', 'Type here...', 'placeholder', 'Type here...'],
            'batchTitle' => ['batchTitle', 'Custom Title', 'batchTitle', 'Custom Title'],
            'batchDescription' => ['batchDescription', 'Custom Description', 'batchDescription', 'Custom Description'],
            'batchIcon' => ['batchIcon', 'fa fa-search', 'batchIcon', 'fa fa-search'],
            'batchButtonText' => ['batchButtonText', 'Import', 'batchButtonText', 'Import'],
            'batchMax' => ['batchMax', 200, 'batchMax', 200],
            'itemLabel' => ['itemLabel', 'phone', 'itemLabel', 'phone'],
            'batchPlaceholder' => ['batchPlaceholder', 'Enter phone numbers...', 'batchPlaceholder', 'Enter phone numbers...'],
            'queryField' => ['queryField', 'emails', 'queryField', 'emails'],
        ];
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

        $this->assertSame('^\d{6}$', $this->getProtectedProperty($batch, 'validationPattern'));
    }

    public function test_validation_pattern_sets_message(): void
    {
        $batch = $this->makeBatchInput();

        $batch->validationPattern('^\d+$', 'Must be numeric');

        $this->assertSame('Must be numeric', $this->getProtectedProperty($batch, 'validationMessage'));
    }

    public function test_validation_pattern_without_message_keeps_empty(): void
    {
        $batch = $this->makeBatchInput();

        $batch->validationPattern('^\d+$');

        $this->assertSame('', $this->getProtectedProperty($batch, 'validationMessage'));
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

        $this->assertSame('App\\Models\\User', $this->getProtectedProperty($batch, 'model'));
    }

    public function test_model_sets_key_and_text(): void
    {
        $batch = $this->makeBatchInput();

        $batch->model('App\\Models\\User', 'uid', 'username');

        $this->assertSame('uid', $this->getProtectedProperty($batch, 'modelKey'));
        $this->assertSame('username', $this->getProtectedProperty($batch, 'modelText'));
    }

    public function test_model_uses_default_key_and_text(): void
    {
        $batch = $this->makeBatchInput();

        $batch->model('App\\Models\\User');

        $this->assertSame('id', $this->getProtectedProperty($batch, 'modelKey'));
        $this->assertSame('name', $this->getProtectedProperty($batch, 'modelText'));
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
        $this->assertSame('Search...', $this->getProtectedProperty($batch, 'placeholder'));
        $this->assertSame('Batch Lookup', $this->getProtectedProperty($batch, 'batchTitle'));
        $this->assertSame(50, $this->getProtectedProperty($batch, 'batchMax'));
        $this->assertSame('Enter IDs here...', $this->getProtectedProperty($batch, 'batchPlaceholder'));
        $this->assertSame('ids', $this->getProtectedProperty($batch, 'queryField'));
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

        $this->assertSame([], $ref->invoke($batch));
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

        $this->assertSame([], $ref->invoke($batch));
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
        $this->assertSame([], $ref->invoke($batch));
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
