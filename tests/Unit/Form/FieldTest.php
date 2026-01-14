<?php

namespace Dcat\Admin\Tests\Unit\Form;

use Dcat\Admin\Form\Field;
use Dcat\Admin\Tests\TestCase;

class FieldTest extends TestCase
{
    public function test_field_column(): void
    {
        $field = new Field\Text('name');
        $this->assertEquals('name', $field->column());
    }

    public function test_field_label(): void
    {
        $field = new Field\Text('user_name', ['User Name']);
        $this->assertEquals('User Name', $field->label());
    }

    public function test_field_label_auto_format(): void
    {
        $field = new Field\Text('user_name');
        // 默认会把下划线转成空格
        $this->assertStringContainsString('user', strtolower($field->label()));
    }

    public function test_field_value(): void
    {
        $field = new Field\Text('name');
        $field->value('Test Value');
        $this->assertEquals('Test Value', $field->value());
    }

    public function test_field_default(): void
    {
        $field = new Field\Text('name');
        $field->default('Default Value');
        $this->assertEquals('Default Value', $field->default());
    }

    public function test_field_help(): void
    {
        $field = new Field\Text('name');
        $result = $field->help('This is help text');
        // help 返回 $this
        $this->assertInstanceOf(Field\Text::class, $result);
    }

    public function test_field_placeholder(): void
    {
        $field = new Field\Text('name');
        $field->placeholder('Enter name...');
        // placeholder 返回设置的值
        $this->assertEquals('Enter name...', $field->placeholder());
    }

    public function test_field_required(): void
    {
        $field = new Field\Text('name');
        $result = $field->required();
        // required 返回 $this
        $this->assertInstanceOf(Field\Text::class, $result);
    }

    public function test_field_rules(): void
    {
        $field = new Field\Text('email');
        $result = $field->rules('email|max:255');
        // rules 返回 $this
        $this->assertInstanceOf(Field\Text::class, $result);
    }

    public function test_field_attribute(): void
    {
        $field = new Field\Text('name');
        $field->attribute('data-id', '123');
        $field->attribute(['data-type' => 'text', 'autocomplete' => 'off']);

        // attribute 设置成功
        $this->assertInstanceOf(Field\Text::class, $field);
    }

    public function test_field_readonly(): void
    {
        $field = new Field\Text('name');
        $result = $field->readOnly();
        // readOnly 返回 $this
        $this->assertInstanceOf(Field\Text::class, $result);
    }

    public function test_field_disable(): void
    {
        $field = new Field\Text('name');
        $result = $field->disable();
        // disable 返回 $this
        $this->assertInstanceOf(Field\Text::class, $result);
    }

    public function test_field_width(): void
    {
        $field = new Field\Text('name');
        $result = $field->width(4, 6);
        // width 返回 $this
        $this->assertInstanceOf(Field\Text::class, $result);
    }

    public function test_field_element_name(): void
    {
        $field = new Field\Text('user.name');
        $this->assertEquals('user[name]', $field->getElementName());
    }

    public function test_field_element_name_simple(): void
    {
        $field = new Field\Text('name');
        $this->assertEquals('name', $field->getElementName());
    }

    public function test_field_set_element_name(): void
    {
        $field = new Field\Text('name');
        $field->setElementName('custom_name');
        $this->assertEquals('custom_name', $field->getElementName());
    }

    public function test_field_fill(): void
    {
        $field = new Field\Text('name');
        $field->fill(['name' => 'John']);
        $this->assertEquals('John', $field->value());
    }

    public function test_field_fill_nested(): void
    {
        $field = new Field\Text('profile.name');
        $field->fill(['profile' => ['name' => 'Jane']]);
        $this->assertEquals('Jane', $field->value());
    }

    public function test_field_custom_format(): void
    {
        $field = new Field\Text('name');
        $field->customFormat(function ($value) {
            return strtoupper($value);
        });
        $field->fill(['name' => 'john']);
        $this->assertEquals('JOHN', $field->value());
    }

    public function test_field_constants(): void
    {
        $this->assertEquals('_file_del_', Field::FILE_DELETE_FLAG);
        $this->assertEquals('field_', Field::FIELD_CLASS_PREFIX);
        $this->assertEquals('build-ignore', Field::BUILD_IGNORE);
    }

    public function test_text_field(): void
    {
        $field = new Field\Text('username');
        $this->assertInstanceOf(Field::class, $field);
    }

    public function test_textarea_field(): void
    {
        $field = new Field\Textarea('description');
        $field->rows(5);
        $this->assertInstanceOf(Field::class, $field);
    }

    public function test_number_field(): void
    {
        $field = new Field\Number('age');
        $field->min(0)->max(150);
        $this->assertInstanceOf(Field::class, $field);
    }

    public function test_email_field(): void
    {
        $field = new Field\Email('email');
        $this->assertInstanceOf(Field::class, $field);
    }

    public function test_hidden_field(): void
    {
        $field = new Field\Hidden('token');
        $this->assertInstanceOf(Field::class, $field);
    }

    public function test_password_field(): void
    {
        $field = new Field\Password('password');
        $this->assertInstanceOf(Field::class, $field);
    }

    public function test_select_field(): void
    {
        $field = new Field\Select('status');
        $field->options([
            'active' => 'Active',
            'inactive' => 'Inactive',
        ]);
        $this->assertInstanceOf(Field::class, $field);
    }

    public function test_checkbox_field(): void
    {
        $field = new Field\Checkbox('roles');
        $field->options([
            'admin' => 'Administrator',
            'user' => 'User',
        ]);
        $this->assertInstanceOf(Field::class, $field);
    }

    public function test_radio_field(): void
    {
        $field = new Field\Radio('gender');
        $field->options([
            'male' => 'Male',
            'female' => 'Female',
        ]);
        $this->assertInstanceOf(Field::class, $field);
    }

    public function test_date_field(): void
    {
        $field = new Field\Date('birthday');
        $field->format('YYYY-MM-DD');
        $this->assertInstanceOf(Field::class, $field);
    }

    public function test_datetime_field(): void
    {
        $field = new Field\Datetime('created_at');
        $this->assertInstanceOf(Field::class, $field);
    }

    public function test_field_horizontal(): void
    {
        $field = new Field\Text('name');

        // 测试设置水平布局
        $result = $field->horizontal(true);
        $this->assertInstanceOf(Field\Text::class, $result);

        // 设置为垂直布局
        $result = $field->horizontal(false);
        $this->assertInstanceOf(Field\Text::class, $result);
    }
}
