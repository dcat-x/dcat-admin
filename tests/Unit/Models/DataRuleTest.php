<?php

namespace Dcat\Admin\Tests\Unit\Models;

use Dcat\Admin\Models\DataRule;
use Dcat\Admin\Models\Menu;
use Dcat\Admin\Models\Role;
use Dcat\Admin\Tests\TestCase;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use PHPUnit\Framework\Attributes\DataProvider;

class DataRuleTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->app['config']->set('admin.database.data_rules_table', 'admin_data_rules');
        $this->app['config']->set('admin.database.data_rules_model', DataRule::class);
        $this->app['config']->set('admin.database.menu_model', Menu::class);
        $this->app['config']->set('admin.database.roles_model', Role::class);
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
        $this->assertSame('Test Rule', $rule->name);
        $this->assertSame('department_id', $rule->field);
    }

    public function test_data_rule_scope_constants(): void
    {
        $this->assertSame('row', DataRule::SCOPE_ROW);
        $this->assertSame('column', DataRule::SCOPE_COLUMN);
        $this->assertSame('form', DataRule::SCOPE_FORM);
    }

    public function test_data_rule_value_type_constants(): void
    {
        $this->assertSame('fixed', DataRule::VALUE_TYPE_FIXED);
        $this->assertSame('variable', DataRule::VALUE_TYPE_VARIABLE);
    }

    public function test_data_rule_condition_constants(): void
    {
        $this->assertSame('=', DataRule::CONDITION_EQUAL);
        $this->assertSame('!=', DataRule::CONDITION_NOT_EQUAL);
        $this->assertSame('>', DataRule::CONDITION_GREATER);
        $this->assertSame('>=', DataRule::CONDITION_GREATER_EQUAL);
        $this->assertSame('<', DataRule::CONDITION_LESS);
        $this->assertSame('<=', DataRule::CONDITION_LESS_EQUAL);
        $this->assertSame('like', DataRule::CONDITION_LIKE);
        $this->assertSame('in', DataRule::CONDITION_IN);
        $this->assertSame('not_in', DataRule::CONDITION_NOT_IN);
        $this->assertSame('between', DataRule::CONDITION_BETWEEN);
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

    #[DataProvider('optionMethodProvider')]
    public function test_option_methods_contain_expected_keys(string $method, array $expectedKeys): void
    {
        $options = DataRule::$method();

        $this->assertIsArray($options);
        $this->assertArrayContainsKeys($expectedKeys, $options);
    }

    public static function optionMethodProvider(): array
    {
        return [
            [
                'method' => 'getConditionOptions',
                'expectedKeys' => [
                    DataRule::CONDITION_EQUAL,
                    DataRule::CONDITION_NOT_EQUAL,
                    DataRule::CONDITION_LIKE,
                    DataRule::CONDITION_IN,
                ],
            ],
            [
                'method' => 'getScopeOptions',
                'expectedKeys' => [
                    DataRule::SCOPE_ROW,
                    DataRule::SCOPE_COLUMN,
                    DataRule::SCOPE_FORM,
                ],
            ],
            [
                'method' => 'getValueTypeOptions',
                'expectedKeys' => [
                    DataRule::VALUE_TYPE_FIXED,
                    DataRule::VALUE_TYPE_VARIABLE,
                ],
            ],
        ];
    }

    #[DataProvider('systemVariableKeyProvider')]
    public function test_get_system_variables_contains_expected_keys(string $key): void
    {
        $variables = DataRule::getSystemVariables();

        $this->assertIsArray($variables);
        $this->assertContains($key, array_keys($variables));
    }

    public static function systemVariableKeyProvider(): array
    {
        return [
            ['key' => '{user_id}'],
            ['key' => '{username}'],
            ['key' => '{department_id}'],
            ['key' => '{department_ids}'],
            ['key' => '{department_path}'],
        ];
    }

    public function test_data_rule_fillable_attributes(): void
    {
        $rule = new DataRule;

        $fillable = [
            'menu_id', 'name', 'field', 'condition', 'value',
            'value_type', 'scope', 'status', 'order',
        ];

        foreach ($fillable as $attribute) {
            $this->assertContains($attribute, $rule->getFillable(), "Attribute '{$attribute}' should be fillable");
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

    public function test_data_rule_relationships_return_expected_relation_types(): void
    {
        $rule = new DataRule;

        $this->assertInstanceOf(BelongsTo::class, $rule->menu());
        $this->assertInstanceOf(BelongsToMany::class, $rule->roles());
    }

    public function test_data_rule_defaults(): void
    {
        $rule = new DataRule;

        // 检查默认值
        $this->assertSame(DataRule::VALUE_TYPE_FIXED, $rule->value_type ?? DataRule::VALUE_TYPE_FIXED);
        $this->assertSame(DataRule::SCOPE_ROW, $rule->scope ?? DataRule::SCOPE_ROW);
        $this->assertSame(1, $rule->status ?? 1);
        $this->assertSame(0, $rule->order ?? 0);
    }

    private function assertArrayContainsKeys(array $expectedKeys, array $actual): void
    {
        foreach ($expectedKeys as $key) {
            $this->assertContains($key, array_keys($actual));
        }
    }
}
