<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Form\Field;

use Dcat\Admin\Exception\RuntimeException;
use Dcat\Admin\Form\Field\Select;
use Dcat\Admin\Models\Administrator;
use Dcat\Admin\Tests\TestCase;
use Mockery;

class SelectTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    protected function createSelect(string $column = 'status', string $label = 'Status'): Select
    {
        return new Select($column, [$label]);
    }

    protected function getProtectedProperty(object $object, string $property)
    {
        $reflection = new \ReflectionProperty($object, $property);
        $reflection->setAccessible(true);

        return $reflection->getValue($object);
    }

    // -------------------------------------------------------
    // options()
    // -------------------------------------------------------

    public function test_options_with_array(): void
    {
        $field = $this->createSelect();
        $options = ['active' => 'Active', 'inactive' => 'Inactive'];

        $result = $field->options($options);

        $this->assertSame($field, $result);
        $this->assertSame($options, $this->getProtectedProperty($field, 'options'));
    }

    public function test_options_with_empty_array(): void
    {
        $field = $this->createSelect();

        $field->options([]);

        $this->assertSame([], $this->getProtectedProperty($field, 'options'));
    }

    public function test_options_with_closure(): void
    {
        $field = $this->createSelect();
        $closure = function () {
            return ['a' => 'A'];
        };

        $result = $field->options($closure);

        $this->assertSame($field, $result);
        $this->assertInstanceOf(\Closure::class, $this->getProtectedProperty($field, 'options'));
    }

    public function test_options_returns_this(): void
    {
        $field = $this->createSelect();

        $result = $field->options(['x' => 'X']);

        $this->assertInstanceOf(Select::class, $result);
    }

    // -------------------------------------------------------
    // groups()
    // -------------------------------------------------------

    public function test_groups_sets_option_groups(): void
    {
        $field = $this->createSelect();
        $groups = [
            [
                'label' => 'Group A',
                'options' => [1 => 'foo', 2 => 'bar'],
            ],
            [
                'label' => 'Group B',
                'options' => [3 => 'baz'],
            ],
        ];

        $result = $field->groups($groups);

        $this->assertSame($field, $result);
        $this->assertSame($groups, $this->getProtectedProperty($field, 'groups'));
    }

    public function test_groups_with_empty_array(): void
    {
        $field = $this->createSelect();

        $field->groups([]);

        $this->assertSame([], $this->getProtectedProperty($field, 'groups'));
    }

    // -------------------------------------------------------
    // config()
    // -------------------------------------------------------

    public function test_config_sets_single_key_value(): void
    {
        $field = $this->createSelect();

        $result = $field->config('allowClear', true);

        $this->assertSame($field, $result);
        $config = $this->getProtectedProperty($field, 'config');
        $this->assertTrue($config['allowClear'] ?? false);
    }

    public function test_config_sets_multiple_keys(): void
    {
        $field = $this->createSelect();

        $field->config('minimumInputLength', 3);
        $field->config('maximumSelectionLength', 5);

        $config = $this->getProtectedProperty($field, 'config');
        $this->assertSame(3, $config['minimumInputLength']);
        $this->assertSame(5, $config['maximumSelectionLength']);
    }

    public function test_config_overwrites_existing_key(): void
    {
        $field = $this->createSelect();

        $field->config('allowClear', true);
        $field->config('allowClear', false);

        $config = $this->getProtectedProperty($field, 'config');
        $this->assertFalse($config['allowClear']);
    }

    // -------------------------------------------------------
    // addDefaultConfig()
    // -------------------------------------------------------

    public function test_add_default_config_sets_value_when_key_not_exists(): void
    {
        $field = $this->createSelect();

        $result = $field->addDefaultConfig('allowClear', true);

        $this->assertSame($field, $result);
        $config = $this->getProtectedProperty($field, 'config');
        $this->assertTrue($config['allowClear']);
    }

    public function test_add_default_config_does_not_overwrite_existing_key(): void
    {
        $field = $this->createSelect();

        $field->config('allowClear', false);
        $field->addDefaultConfig('allowClear', true);

        $config = $this->getProtectedProperty($field, 'config');
        $this->assertFalse($config['allowClear']);
    }

    public function test_add_default_config_with_array(): void
    {
        $field = $this->createSelect();

        $field->config('allowClear', false);
        $field->addDefaultConfig([
            'allowClear' => true,
            'minimumInputLength' => 1,
        ]);

        $config = $this->getProtectedProperty($field, 'config');
        // allowClear should remain false (already set)
        $this->assertFalse($config['allowClear']);
        // minimumInputLength should be set (not previously set)
        $this->assertSame(1, $config['minimumInputLength']);
    }

    // -------------------------------------------------------
    // disableClearButton()
    // -------------------------------------------------------

    public function test_disable_clear_button(): void
    {
        $field = $this->createSelect();

        $result = $field->disableClearButton();

        $this->assertSame($field, $result);
        $config = $this->getProtectedProperty($field, 'config');
        $this->assertFalse($config['allowClear'] ?? null);
    }

    public function test_disable_clear_button_overrides_previous_allow_clear(): void
    {
        $field = $this->createSelect();

        $field->config('allowClear', true);
        $field->disableClearButton();

        $config = $this->getProtectedProperty($field, 'config');
        $this->assertFalse($config['allowClear']);
    }

    // -------------------------------------------------------
    // ajax()
    // -------------------------------------------------------

    public function test_ajax_sets_url_and_variables(): void
    {
        $field = $this->createSelect();

        $result = $field->ajax('/api/users');

        $this->assertSame($field, $result);

        $variables = $this->getProtectedProperty($field, 'variables');
        $this->assertSame('id', $variables['ajax']['idField'] ?? null);
        $this->assertSame('text', $variables['ajax']['textField'] ?? null);
    }

    public function test_ajax_sets_custom_id_and_text_fields(): void
    {
        $field = $this->createSelect();

        $field->ajax('/api/users', 'user_id', 'username');

        $variables = $this->getProtectedProperty($field, 'variables');
        $this->assertSame('user_id', $variables['ajax']['idField']);
        $this->assertSame('username', $variables['ajax']['textField']);
    }

    public function test_ajax_adds_default_minimum_input_length(): void
    {
        $field = $this->createSelect();

        $field->ajax('/api/users');

        $config = $this->getProtectedProperty($field, 'config');
        $this->assertSame(1, $config['minimumInputLength'] ?? null);
    }

    public function test_ajax_does_not_overwrite_existing_minimum_input_length(): void
    {
        $field = $this->createSelect();

        $field->config('minimumInputLength', 3);
        $field->ajax('/api/users');

        $config = $this->getProtectedProperty($field, 'config');
        $this->assertSame(3, $config['minimumInputLength']);
    }

    // -------------------------------------------------------
    // placeholder()
    // -------------------------------------------------------

    public function test_placeholder_setter(): void
    {
        $field = $this->createSelect();

        $result = $field->placeholder('Please select...');

        $this->assertSame($field, $result);
    }

    public function test_placeholder_getter_returns_set_value(): void
    {
        $field = $this->createSelect();

        $field->placeholder('Please select...');
        $value = $field->placeholder();

        $this->assertSame('Please select...', $value);
    }

    public function test_placeholder_getter_returns_label_when_not_set(): void
    {
        $field = $this->createSelect('status', 'Status');

        $value = $field->placeholder();

        $this->assertSame('Status', $value);
    }

    // -------------------------------------------------------
    // cascadeEvent default
    // -------------------------------------------------------

    public function test_cascade_event_defaults_to_change(): void
    {
        $field = $this->createSelect();

        $cascadeEvent = $this->getProtectedProperty($field, 'cascadeEvent');

        $this->assertSame('change', $cascadeEvent);
    }

    // -------------------------------------------------------
    // model()
    // -------------------------------------------------------

    public function test_model_throws_on_invalid_class(): void
    {
        $field = $this->createSelect();

        $this->expectException(RuntimeException::class);

        $field->model('App\\Models\\NonExistentClass');
    }

    public function test_model_throws_on_non_model_class(): void
    {
        $field = $this->createSelect();

        $this->expectException(RuntimeException::class);

        $field->model(\stdClass::class);
    }

    public function test_model_sets_options_closure(): void
    {
        $field = $this->createSelect();

        $result = $field->model(Administrator::class);

        $this->assertSame($field, $result);
        $this->assertInstanceOf(\Closure::class, $this->getProtectedProperty($field, 'options'));
    }
}
