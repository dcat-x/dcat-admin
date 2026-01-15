<?php

namespace Dcat\Admin\Tests\Unit\Models;

use Dcat\Admin\Models\DataRule;
use Dcat\Admin\Tests\TestCase;

class DataRuleTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->app['config']->set('admin.database.data_rules_table', 'admin_data_rules');
        $this->app['config']->set('admin.database.data_rules_model', DataRule::class);
    }

    public function test_data_rule_creation(): void
    {
        $rule = new DataRule([
            'menu_id' => 1,
            'name' => 'Test Rule',
            'field' => 'department_id',
            'condition' => '=',
            'value' => '{department_id}',
            'value_type' => 'variable',
            'scope' => 'row',
            'status' => 1,
        ]);

        $this->assertInstanceOf(DataRule::class, $rule);
        $this->assertEquals('Test Rule', $rule->name);
        $this->assertEquals('department_id', $rule->field);
    }

    public function test_data_rule_scope_constants(): void
    {
        $this->assertEquals('row', DataRule::SCOPE_ROW);
        $this->assertEquals('column', DataRule::SCOPE_COLUMN);
        $this->assertEquals('form', DataRule::SCOPE_FORM);
    }

    public function test_data_rule_value_type_constants(): void
    {
        $this->assertEquals('fixed', DataRule::VALUE_TYPE_FIXED);
        $this->assertEquals('variable', DataRule::VALUE_TYPE_VARIABLE);
    }

    public function test_data_rule_condition_constants(): void
    {
        $this->assertEquals('=', DataRule::CONDITION_EQUAL);
        $this->assertEquals('!=', DataRule::CONDITION_NOT_EQUAL);
        $this->assertEquals('>', DataRule::CONDITION_GREATER);
        $this->assertEquals('>=', DataRule::CONDITION_GREATER_EQUAL);
        $this->assertEquals('<', DataRule::CONDITION_LESS);
        $this->assertEquals('<=', DataRule::CONDITION_LESS_EQUAL);
        $this->assertEquals('like', DataRule::CONDITION_LIKE);
        $this->assertEquals('in', DataRule::CONDITION_IN);
        $this->assertEquals('not_in', DataRule::CONDITION_NOT_IN);
        $this->assertEquals('between', DataRule::CONDITION_BETWEEN);
    }

    public function test_is_row_scope(): void
    {
        $rowRule = new DataRule(['scope' => DataRule::SCOPE_ROW]);
        $columnRule = new DataRule(['scope' => DataRule::SCOPE_COLUMN]);

        $this->assertTrue($rowRule->isRowScope());
        $this->assertFalse($columnRule->isRowScope());
    }

    public function test_is_column_scope(): void
    {
        $columnRule = new DataRule(['scope' => DataRule::SCOPE_COLUMN]);
        $rowRule = new DataRule(['scope' => DataRule::SCOPE_ROW]);

        $this->assertTrue($columnRule->isColumnScope());
        $this->assertFalse($rowRule->isColumnScope());
    }

    public function test_is_form_scope(): void
    {
        $formRule = new DataRule(['scope' => DataRule::SCOPE_FORM]);
        $rowRule = new DataRule(['scope' => DataRule::SCOPE_ROW]);

        $this->assertTrue($formRule->isFormScope());
        $this->assertFalse($rowRule->isFormScope());
    }

    public function test_is_enabled(): void
    {
        $enabledRule = new DataRule(['status' => 1]);
        $disabledRule = new DataRule(['status' => 0]);

        $this->assertTrue($enabledRule->isEnabled());
        $this->assertFalse($disabledRule->isEnabled());
    }

    public function test_is_variable_value(): void
    {
        $variableRule = new DataRule(['value_type' => DataRule::VALUE_TYPE_VARIABLE]);
        $fixedRule = new DataRule(['value_type' => DataRule::VALUE_TYPE_FIXED]);

        $this->assertTrue($variableRule->isVariableValue());
        $this->assertFalse($fixedRule->isVariableValue());
    }

    public function test_get_condition_options(): void
    {
        $options = DataRule::getConditionOptions();

        $this->assertIsArray($options);
        $this->assertArrayHasKey(DataRule::CONDITION_EQUAL, $options);
        $this->assertArrayHasKey(DataRule::CONDITION_NOT_EQUAL, $options);
        $this->assertArrayHasKey(DataRule::CONDITION_LIKE, $options);
        $this->assertArrayHasKey(DataRule::CONDITION_IN, $options);
    }

    public function test_get_scope_options(): void
    {
        $options = DataRule::getScopeOptions();

        $this->assertIsArray($options);
        $this->assertArrayHasKey(DataRule::SCOPE_ROW, $options);
        $this->assertArrayHasKey(DataRule::SCOPE_COLUMN, $options);
        $this->assertArrayHasKey(DataRule::SCOPE_FORM, $options);
    }

    public function test_get_value_type_options(): void
    {
        $options = DataRule::getValueTypeOptions();

        $this->assertIsArray($options);
        $this->assertArrayHasKey(DataRule::VALUE_TYPE_FIXED, $options);
        $this->assertArrayHasKey(DataRule::VALUE_TYPE_VARIABLE, $options);
    }

    public function test_get_system_variables(): void
    {
        $variables = DataRule::getSystemVariables();

        $this->assertIsArray($variables);
        $this->assertArrayHasKey('{user_id}', $variables);
        $this->assertArrayHasKey('{username}', $variables);
        $this->assertArrayHasKey('{department_id}', $variables);
        $this->assertArrayHasKey('{department_ids}', $variables);
        $this->assertArrayHasKey('{department_path}', $variables);
    }

    public function test_data_rule_fillable_attributes(): void
    {
        $rule = new DataRule;

        $fillable = [
            'menu_id', 'name', 'field', 'condition', 'value',
            'value_type', 'scope', 'status', 'order'
        ];

        foreach ($fillable as $attribute) {
            $this->assertTrue(
                in_array($attribute, $rule->getFillable()),
                "Attribute '{$attribute}' should be fillable"
            );
        }
    }

    public function test_data_rule_casts(): void
    {
        $rule = new DataRule([
            'menu_id' => '1',
            'status' => '1',
            'order' => '5',
        ]);

        $this->assertIsInt($rule->menu_id);
        $this->assertIsInt($rule->status);
        $this->assertIsInt($rule->order);
    }

    public function test_data_rule_relationships_exist(): void
    {
        $rule = new DataRule;

        $this->assertTrue(method_exists($rule, 'menu'));
        $this->assertTrue(method_exists($rule, 'roles'));
    }

    public function test_data_rule_defaults(): void
    {
        $rule = new DataRule;

        // 检查默认值
        $this->assertEquals('fixed', $rule->value_type ?? 'fixed');
        $this->assertEquals('row', $rule->scope ?? 'row');
        $this->assertEquals(1, $rule->status ?? 1);
        $this->assertEquals(0, $rule->order ?? 0);
    }
}
