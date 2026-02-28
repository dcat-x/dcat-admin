<?php

namespace Dcat\Admin\Tests\Unit\Form\Field;

use Dcat\Admin\Form;
use Dcat\Admin\Form\Builder;
use Dcat\Admin\Form\Field\HasMany;
use Dcat\Admin\Form\NestedForm;
use Dcat\Admin\Tests\TestCase;
use Mockery;

/**
 * 测试 HasMany 表单字段的关键方法。
 */
class HasManyTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /**
     * 辅助方法：通过 Reflection 读取 protected/private 属性。
     */
    protected function getProperty(object $object, string $property): mixed
    {
        $ref = new \ReflectionProperty($object, $property);
        $ref->setAccessible(true);

        return $ref->getValue($object);
    }

    /**
     * 辅助方法：通过 Reflection 设置 protected/private 属性。
     */
    protected function setProperty(object $object, string $property, mixed $value): void
    {
        $ref = new \ReflectionProperty($object, $property);
        $ref->setAccessible(true);
        $ref->setValue($object, $value);
    }

    /**
     * 辅助方法：调用 protected/private 方法。
     */
    protected function invokeMethod(object $object, string $method, array $args = []): mixed
    {
        $ref = new \ReflectionMethod($object, $method);
        $ref->setAccessible(true);

        return $ref->invoke($object, ...$args);
    }

    /**
     * 辅助方法：创建 mock Form，使 NestedForm::pushField 中
     * 对 $this->form->builder()->pushField() 的调用不会报错。
     */
    protected function createMockForm(): Form
    {
        $mockBuilder = Mockery::mock(Builder::class);
        $mockBuilder->shouldReceive('pushField')->andReturnSelf();

        $mockForm = Mockery::mock(Form::class);
        $mockForm->shouldReceive('builder')->andReturn($mockBuilder);
        $mockForm->shouldReceive('getKey')->andReturnNull();

        return $mockForm;
    }

    // ---------------------------------------------------------------
    // 1. 构造函数测试
    // ---------------------------------------------------------------

    public function test_constructor_with_closure_only(): void
    {
        $builder = function (NestedForm $form) {};

        $field = new HasMany('items', [$builder]);

        $this->assertSame('items', $this->getProperty($field, 'relationName'));
        $this->assertSame('items', $this->getProperty($field, 'column'));
        $this->assertSame($builder, $this->getProperty($field, 'builder'));
        // label 应通过 formatLabel() 自动生成（不是空字符串）
        $this->assertNotEmpty($this->getProperty($field, 'label'));
    }

    public function test_constructor_with_label_and_closure(): void
    {
        $builder = function (NestedForm $form) {};

        $field = new HasMany('items', ['My Items', $builder]);

        $this->assertSame('items', $this->getProperty($field, 'relationName'));
        $this->assertSame('items', $this->getProperty($field, 'column'));
        $this->assertSame('My Items', $this->getProperty($field, 'label'));
        $this->assertSame($builder, $this->getProperty($field, 'builder'));
    }

    public function test_constructor_sets_column_class(): void
    {
        $builder = function () {};

        $field = new HasMany('order.items', [$builder]);

        // columnClass 应将 `.` 替换为 `-`
        $this->assertSame('order-items', $this->getProperty($field, 'columnClass'));
    }

    // ---------------------------------------------------------------
    // 2. mode() / useTab() / useTable() 视图模式切换
    // ---------------------------------------------------------------

    public function test_mode_sets_view_mode(): void
    {
        $field = new HasMany('items', [function () {}]);

        $result = $field->mode('tab');

        $this->assertSame($field, $result, 'mode() 应返回 $this 以支持链式调用');
        $this->assertSame('tab', $this->getProperty($field, 'viewMode'));
    }

    public function test_use_tab_sets_tab_mode(): void
    {
        $field = new HasMany('items', [function () {}]);

        $result = $field->useTab();

        $this->assertSame($field, $result);
        $this->assertSame('tab', $this->getProperty($field, 'viewMode'));
    }

    public function test_use_table_sets_table_mode(): void
    {
        $field = new HasMany('items', [function () {}]);

        $result = $field->useTable();

        $this->assertSame($field, $result);
        $this->assertSame('table', $this->getProperty($field, 'viewMode'));
    }

    public function test_default_view_mode_is_default(): void
    {
        $field = new HasMany('items', [function () {}]);

        $this->assertSame('default', $this->getProperty($field, 'viewMode'));
    }

    // ---------------------------------------------------------------
    // 3. disableCreate() / disableDelete() 选项控制
    // ---------------------------------------------------------------

    public function test_disable_create(): void
    {
        $field = new HasMany('items', [function () {}]);

        // 默认允许创建
        $options = $this->getProperty($field, 'options');
        $this->assertTrue($options['allowCreate']);

        $result = $field->disableCreate();

        $this->assertSame($field, $result, 'disableCreate() 应返回 $this');
        $options = $this->getProperty($field, 'options');
        $this->assertFalse($options['allowCreate']);
    }

    public function test_disable_delete(): void
    {
        $field = new HasMany('items', [function () {}]);

        // 默认允许删除
        $options = $this->getProperty($field, 'options');
        $this->assertTrue($options['allowDelete']);

        $result = $field->disableDelete();

        $this->assertSame($field, $result, 'disableDelete() 应返回 $this');
        $options = $this->getProperty($field, 'options');
        $this->assertFalse($options['allowDelete']);
    }

    public function test_disable_both_create_and_delete(): void
    {
        $field = new HasMany('items', [function () {}]);

        $field->disableCreate()->disableDelete();

        $options = $this->getProperty($field, 'options');
        $this->assertFalse($options['allowCreate']);
        $this->assertFalse($options['allowDelete']);
    }

    // ---------------------------------------------------------------
    // 4. value() 始终返回数组
    // ---------------------------------------------------------------

    public function test_value_returns_array_when_no_value_set(): void
    {
        $field = new HasMany('items', [function () {}]);

        $result = $field->value();

        $this->assertIsArray($result);
    }

    public function test_value_setter_returns_field_instance(): void
    {
        $field = new HasMany('items', [function () {}]);

        $result = $field->value([['id' => 1, 'name' => 'foo']]);

        $this->assertSame($field, $result);
    }

    public function test_value_getter_returns_array_after_set(): void
    {
        $field = new HasMany('items', [function () {}]);
        $data = [['id' => 1, 'name' => 'foo'], ['id' => 2, 'name' => 'bar']];

        $field->value($data);

        $this->assertSame($data, $field->value());
    }

    public function test_value_wraps_non_array_as_array(): void
    {
        $field = new HasMany('items', [function () {}]);

        // 设置一个字符串值
        $field->value('some_string');

        $result = $field->value();
        $this->assertIsArray($result);
    }

    // ---------------------------------------------------------------
    // 5. setRelationKeyName() / getKeyName() 关系键名
    // ---------------------------------------------------------------

    public function test_default_relation_key_name(): void
    {
        $field = new HasMany('items', [function () {}]);

        $this->assertSame('id', $this->getProperty($field, 'relationKeyName'));
    }

    public function test_set_relation_key_name(): void
    {
        $field = new HasMany('items', [function () {}]);

        $result = $field->setRelationKeyName('custom_id');

        $this->assertSame($field, $result, 'setRelationKeyName() 应返回 $this');
        $this->assertSame('custom_id', $this->getProperty($field, 'relationKeyName'));
    }

    public function test_get_key_name_returns_null_when_no_form(): void
    {
        $field = new HasMany('items', [function () {}]);

        // 没有设置 form 时，getKeyName() 应返回 null
        $this->assertNull($field->getKeyName());
    }

    public function test_get_key_name_returns_relation_key_when_form_set(): void
    {
        $field = new HasMany('items', [function () {}]);

        $form = Mockery::mock(Form::class);
        $field->setForm($form);

        $this->assertSame('id', $field->getKeyName());
    }

    public function test_get_key_name_with_custom_relation_key(): void
    {
        $field = new HasMany('items', [function () {}]);

        $form = Mockery::mock(Form::class);
        $field->setForm($form);
        $field->setRelationKeyName('item_id');

        $this->assertSame('item_id', $field->getKeyName());
    }

    public function test_set_relation_key_name_to_null(): void
    {
        $field = new HasMany('items', [function () {}]);

        $field->setRelationKeyName(null);

        $this->assertNull($this->getProperty($field, 'relationKeyName'));
    }

    // ---------------------------------------------------------------
    // 6. setParentRelationName() / getNestedFormColumnName() 嵌套列名
    // ---------------------------------------------------------------

    public function test_get_nested_form_column_name_without_parent(): void
    {
        $field = new HasMany('items', [function () {}]);

        // 没有 parentRelationName 时，直接返回 column
        $this->assertSame('items', $field->getNestedFormColumnName());
    }

    public function test_set_parent_relation_name(): void
    {
        $field = new HasMany('items', [function () {}]);

        $result = $field->setParentRelationName('order', 123);

        $this->assertSame($field, $result, 'setParentRelationName() 应返回 $this');
        $this->assertSame('order', $this->getProperty($field, 'parentRelationName'));
        $this->assertSame(123, $this->getProperty($field, 'parentKey'));
    }

    public function test_get_nested_form_column_name_with_parent_and_key(): void
    {
        $field = new HasMany('items', [function () {}]);

        $field->setParentRelationName('order', 'pk_42');

        $this->assertSame('order.pk_42.items', $field->getNestedFormColumnName());
    }

    public function test_get_nested_form_column_name_with_parent_and_null_key(): void
    {
        $field = new HasMany('items', [function () {}]);

        $field->setParentRelationName('order', null);

        // parentKey 为 null 时，使用默认的 key 前缀+名称
        $expectedKey = NestedForm::DEFAULT_KEY_PREFIX.NestedForm::DEFAULT_PARENT_KEY_NAME;
        $this->assertSame('order.'.$expectedKey.'.items', $field->getNestedFormColumnName());
    }

    // ---------------------------------------------------------------
    // 7. formatClass() 将 `.` 替换为 `-`
    // ---------------------------------------------------------------

    public function test_format_class_replaces_dots_with_dashes(): void
    {
        $field = new HasMany('items', [function () {}]);

        $result = $this->invokeMethod($field, 'formatClass', ['order.items.details']);

        $this->assertSame('order-items-details', $result);
    }

    public function test_format_class_no_dots(): void
    {
        $field = new HasMany('items', [function () {}]);

        $result = $this->invokeMethod($field, 'formatClass', ['simple']);

        $this->assertSame('simple', $result);
    }

    public function test_format_class_multiple_consecutive_dots(): void
    {
        $field = new HasMany('items', [function () {}]);

        $result = $this->invokeMethod($field, 'formatClass', ['a..b...c']);

        $this->assertSame('a--b---c', $result);
    }

    // ---------------------------------------------------------------
    // 8. buildNestedForm() 构建嵌套表单
    // ---------------------------------------------------------------

    public function test_build_nested_form_returns_nested_form(): void
    {
        $builderCalled = false;
        $builder = function (NestedForm $form) use (&$builderCalled) {
            $builderCalled = true;
        };

        $field = new HasMany('items', [$builder]);
        $field->setForm($this->createMockForm());

        $nestedForm = $field->buildNestedForm();

        $this->assertInstanceOf(NestedForm::class, $nestedForm);
        $this->assertTrue($builderCalled, 'builder 闭包应被调用');
    }

    public function test_build_nested_form_with_key(): void
    {
        $builder = function (NestedForm $form) {};
        $field = new HasMany('items', [$builder]);
        $field->setForm($this->createMockForm());

        $nestedForm = $field->buildNestedForm('custom_key');

        $this->assertInstanceOf(NestedForm::class, $nestedForm);
    }

    public function test_build_nested_form_passes_builder_closure(): void
    {
        $receivedForm = null;
        $builder = function (NestedForm $form) use (&$receivedForm) {
            $receivedForm = $form;
            $form->text('name');
        };

        $field = new HasMany('items', [$builder]);
        $field->setForm($this->createMockForm());

        $nestedForm = $field->buildNestedForm();

        $this->assertNotNull($receivedForm, 'builder 应接收 NestedForm 实例');
        $this->assertInstanceOf(NestedForm::class, $receivedForm);
        // NestedForm 应包含 builder 中定义的字段 + 隐藏字段
        $fields = $nestedForm->fields();
        $this->assertNotEmpty($fields);
    }

    public function test_build_nested_form_uses_nested_column_name(): void
    {
        $builder = function (NestedForm $form) {};
        $field = new HasMany('items', [$builder]);

        $field->setParentRelationName('order', 'pk_1');
        $field->setForm($this->createMockForm());

        $nestedForm = $field->buildNestedForm();

        // NestedForm 的 relationName 应为嵌套列名
        $nestedRelation = $this->getProperty($nestedForm, 'relationName');
        $this->assertSame('order.pk_1.items', $nestedRelation);
    }

    // ---------------------------------------------------------------
    // 9. getValidator() 验证逻辑
    // ---------------------------------------------------------------

    public function test_get_validator_returns_false_when_column_not_in_input(): void
    {
        $field = new HasMany('items', [function (NestedForm $form) {}]);
        $field->setForm($this->createMockForm());

        // input 中不包含 'items' 键
        $result = $field->getValidator(['other_field' => 'value']);

        $this->assertFalse($result);
    }

    public function test_get_validator_returns_false_when_no_rules(): void
    {
        $field = new HasMany('items', [function (NestedForm $form) {
            // 添加字段但不设置验证规则
            $form->text('name');
        }]);
        $field->setForm($this->createMockForm());

        // input 中包含 'items' 键，但嵌套字段没有验证规则
        $input = [
            'items' => [
                0 => ['name' => 'foo', NestedForm::REMOVE_FLAG_NAME => 0],
            ],
        ];

        $result = $field->getValidator($input);

        $this->assertFalse($result);
    }

    // ---------------------------------------------------------------
    // 10. prepareInputValue() — protected 方法
    // ---------------------------------------------------------------

    public function test_prepare_input_value_returns_array_values(): void
    {
        $field = new HasMany('items', [function (NestedForm $form) {
            $form->text('name');
        }]);
        $field->setForm($this->createMockForm());

        // 设置 original 数据（prepareInputValue 内部会用到）
        $this->setProperty($field, 'original', []);

        $input = [
            'some_key' => ['name' => 'foo', NestedForm::REMOVE_FLAG_NAME => 0],
            'another_key' => ['name' => 'bar', NestedForm::REMOVE_FLAG_NAME => 0],
        ];

        $result = $this->invokeMethod($field, 'prepareInputValue', [$input]);

        // 验证返回的是 array_values 的结果（连续整数索引）
        $this->assertIsArray($result);
        $this->assertSame(array_keys($result), range(0, count($result) - 1));
    }

    // ---------------------------------------------------------------
    // 11. buildRelatedForms() — protected 方法
    // ---------------------------------------------------------------

    public function test_build_related_forms_returns_empty_when_form_is_null(): void
    {
        $field = new HasMany('items', [function (NestedForm $form) {}]);

        // 不调用 setForm()，form 属性保持 null
        $result = $this->invokeMethod($field, 'buildRelatedForms');

        $this->assertSame([], $result);
    }

    // ---------------------------------------------------------------
    // 12. getNestedFormDefaultKeyName() — protected 方法
    // ---------------------------------------------------------------

    public function test_get_nested_form_default_key_name_returns_null_without_parent(): void
    {
        $field = new HasMany('items', [function () {}]);

        // 没有设置 parentRelationName 时，方法应返回 null
        $result = $this->invokeMethod($field, 'getNestedFormDefaultKeyName');

        $this->assertNull($result);
    }

    public function test_get_nested_form_default_key_name_returns_key_with_parent(): void
    {
        $field = new HasMany('items', [function () {}]);

        $field->setParentRelationName('order', 'pk_1');

        $result = $this->invokeMethod($field, 'getNestedFormDefaultKeyName');

        $this->assertSame('order_NKEY_', $result);
    }
}
