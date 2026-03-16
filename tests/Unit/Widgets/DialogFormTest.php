<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Widgets;

use Dcat\Admin\Tests\TestCase;
use Dcat\Admin\Widgets\DialogForm;

class DialogFormTest extends TestCase
{
    public function test_dialog_form_creation(): void
    {
        $form = new DialogForm;
        $this->assertInstanceOf(DialogForm::class, $form);
    }

    public function test_dialog_form_with_title(): void
    {
        $form = new DialogForm('My Form');

        $reflection = new \ReflectionClass($form);
        $property = $reflection->getProperty('options');
        $property->setAccessible(true);

        $options = $property->getValue($form);
        $this->assertSame('My Form', $options['title']);
    }

    public function test_dialog_form_title_method(): void
    {
        $form = new DialogForm;
        $result = $form->title('New Title');

        $this->assertSame($form, $result);

        $reflection = new \ReflectionClass($form);
        $property = $reflection->getProperty('options');
        $property->setAccessible(true);

        $options = $property->getValue($form);
        $this->assertSame('New Title', $options['title']);
    }

    public function test_dialog_form_default_options(): void
    {
        $form = new DialogForm;

        $reflection = new \ReflectionClass($form);
        $property = $reflection->getProperty('options');
        $property->setAccessible(true);

        $options = $property->getValue($form);
        // Constructor calls title(null), so title becomes null
        $this->assertNull($options['title']);
        $this->assertSame(['700px', '670px'], $options['area']);
        $this->assertNull($options['defaultUrl']);
        $this->assertNull($options['buttonSelector']);
        $this->assertFalse($options['forceRefresh']);
        $this->assertTrue($options['resetButton']);
    }

    public function test_dialog_form_click(): void
    {
        $form = new DialogForm;
        $result = $form->click('.open-dialog');

        $this->assertSame($form, $result);

        $reflection = new \ReflectionClass($form);
        $property = $reflection->getProperty('options');
        $property->setAccessible(true);

        $options = $property->getValue($form);
        $this->assertSame('.open-dialog', $options['buttonSelector']);
    }

    public function test_dialog_form_force_refresh(): void
    {
        $form = new DialogForm;
        $result = $form->forceRefresh();

        $this->assertSame($form, $result);

        $reflection = new \ReflectionClass($form);
        $property = $reflection->getProperty('options');
        $property->setAccessible(true);

        $options = $property->getValue($form);
        $this->assertTrue($options['forceRefresh']);
    }

    public function test_dialog_form_reset_button(): void
    {
        $form = new DialogForm;
        $result = $form->resetButton(false);

        $this->assertSame($form, $result);

        $reflection = new \ReflectionClass($form);
        $property = $reflection->getProperty('options');
        $property->setAccessible(true);

        $options = $property->getValue($form);
        $this->assertFalse($options['resetButton']);
    }

    public function test_dialog_form_reset_button_default_true(): void
    {
        $form = new DialogForm;
        $form->resetButton();

        $reflection = new \ReflectionClass($form);
        $property = $reflection->getProperty('options');
        $property->setAccessible(true);

        $options = $property->getValue($form);
        $this->assertTrue($options['resetButton']);
    }

    public function test_dialog_form_dimensions(): void
    {
        $form = new DialogForm;
        $result = $form->dimensions('800px', '500px');

        $this->assertSame($form, $result);

        $reflection = new \ReflectionClass($form);
        $property = $reflection->getProperty('options');
        $property->setAccessible(true);

        $options = $property->getValue($form);
        $this->assertSame(['800px', '500px'], $options['area']);
    }

    public function test_dialog_form_width(): void
    {
        $form = new DialogForm;
        $result = $form->width('900px');

        $this->assertSame($form, $result);

        $reflection = new \ReflectionClass($form);
        $property = $reflection->getProperty('options');
        $property->setAccessible(true);

        $options = $property->getValue($form);
        $this->assertSame('900px', $options['area'][0]);
        // Height should remain default
        $this->assertSame('670px', $options['area'][1]);
    }

    public function test_dialog_form_height(): void
    {
        $form = new DialogForm;
        $result = $form->height('400px');

        $this->assertSame($form, $result);

        $reflection = new \ReflectionClass($form);
        $property = $reflection->getProperty('options');
        $property->setAccessible(true);

        $options = $property->getValue($form);
        $this->assertSame('700px', $options['area'][0]);
        $this->assertSame('400px', $options['area'][1]);
    }

    public function test_dialog_form_saved_handler(): void
    {
        $form = new DialogForm;
        $result = $form->saved('console.log("saved")');

        $this->assertSame($form, $result);

        $reflection = new \ReflectionClass($form);
        $property = $reflection->getProperty('handlers');
        $property->setAccessible(true);

        $handlers = $property->getValue($form);
        $this->assertSame('console.log("saved")', $handlers['saved']);
    }

    public function test_dialog_form_success_handler(): void
    {
        $form = new DialogForm;
        $result = $form->success('alert("ok")');

        $this->assertSame($form, $result);

        $reflection = new \ReflectionClass($form);
        $property = $reflection->getProperty('handlers');
        $property->setAccessible(true);

        $handlers = $property->getValue($form);
        $this->assertSame('alert("ok")', $handlers['success']);
    }

    public function test_dialog_form_error_handler(): void
    {
        $form = new DialogForm;
        $result = $form->error('alert("error")');

        $this->assertSame($form, $result);

        $reflection = new \ReflectionClass($form);
        $property = $reflection->getProperty('handlers');
        $property->setAccessible(true);

        $handlers = $property->getValue($form);
        $this->assertSame('alert("error")', $handlers['error']);
    }

    public function test_dialog_form_default_handlers(): void
    {
        $form = new DialogForm;

        $reflection = new \ReflectionClass($form);
        $property = $reflection->getProperty('handlers');
        $property->setAccessible(true);

        $handlers = $property->getValue($form);
        $this->assertNull($handlers['saved']);
        $this->assertNull($handlers['success']);
        $this->assertNull($handlers['error']);
    }

    public function test_dialog_form_options_merge(): void
    {
        $form = new DialogForm;
        $result = $form->options(['title' => 'Merged', 'forceRefresh' => true]);

        $this->assertSame($form, $result);

        $reflection = new \ReflectionClass($form);
        $property = $reflection->getProperty('options');
        $property->setAccessible(true);

        $options = $property->getValue($form);
        $this->assertSame('Merged', $options['title']);
        $this->assertTrue($options['forceRefresh']);
        // Other defaults should remain
        $this->assertTrue($options['resetButton']);
    }

    public function test_dialog_form_query_name_constant(): void
    {
        $this->assertSame('_dialog_form_', DialogForm::QUERY_NAME);
    }

    public function test_dialog_form_content_view(): void
    {
        $this->assertSame('admin::layouts.form-content', DialogForm::$contentView);
    }

    public function test_dialog_form_chaining(): void
    {
        $form = (new DialogForm)
            ->title('Chain Test')
            ->click('#open-btn')
            ->dimensions('50%', '60%')
            ->forceRefresh()
            ->resetButton(false)
            ->saved('console.log("done")')
            ->success('location.reload()')
            ->error('alert("fail")');

        $reflection = new \ReflectionClass($form);

        $optionsProp = $reflection->getProperty('options');
        $optionsProp->setAccessible(true);
        $options = $optionsProp->getValue($form);

        $this->assertSame('Chain Test', $options['title']);
        $this->assertSame('#open-btn', $options['buttonSelector']);
        $this->assertSame(['50%', '60%'], $options['area']);
        $this->assertTrue($options['forceRefresh']);
        $this->assertFalse($options['resetButton']);

        $handlersProp = $reflection->getProperty('handlers');
        $handlersProp->setAccessible(true);
        $handlers = $handlersProp->getValue($form);

        $this->assertSame('console.log("done")', $handlers['saved']);
        $this->assertSame('location.reload()', $handlers['success']);
        $this->assertSame('alert("fail")', $handlers['error']);
    }
}
