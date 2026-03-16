<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Form;

use Dcat\Admin\Form;
use Dcat\Admin\Form\BlockForm;
use Dcat\Admin\Form\Builder as FormBuilder;
use Dcat\Admin\Form\Layout;
use Dcat\Admin\Tests\TestCase;
use Mockery;

class BlockFormTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    protected function createBlockForm(): BlockForm
    {
        $form = Mockery::mock(Form::class);
        $layout = Mockery::mock(Layout::class);
        $layout->shouldReceive('hasColumns')->andReturn(false);
        $layout->shouldReceive('hasBlocks')->andReturn(false);

        $builder = Mockery::mock(FormBuilder::class);
        $builder->shouldReceive('layout')->andReturn($layout);
        $builder->shouldReceive('pushField')->andReturnSelf();

        $form->shouldReceive('builder')->andReturn($builder);
        $form->shouldReceive('getKey')->andReturn(1);
        $form->shouldReceive('model')->andReturn(null);
        $form->shouldReceive('rows')->andReturn([]);

        return new BlockForm($form);
    }

    public function test_constructor_creates_instance(): void
    {
        $blockForm = $this->createBlockForm();

        $this->assertInstanceOf(BlockForm::class, $blockForm);
    }

    public function test_title_sets_and_returns_this(): void
    {
        $blockForm = $this->createBlockForm();

        $result = $blockForm->title('My Block Title');
        $this->assertSame($blockForm, $result);
    }

    public function test_show_footer_returns_this(): void
    {
        $blockForm = $this->createBlockForm();

        $result = $blockForm->showFooter();
        $this->assertSame($blockForm, $result);
    }

    public function test_get_key_delegates_to_form(): void
    {
        $blockForm = $this->createBlockForm();

        $this->assertSame(1, $blockForm->getKey());
    }

    public function test_model_delegates_to_form(): void
    {
        $blockForm = $this->createBlockForm();

        $this->assertNull($blockForm->model());
    }

    public function test_fill_fields_accepts_array(): void
    {
        $blockForm = $this->createBlockForm();

        // fillFields is an empty method, just verify it doesn't throw
        $blockForm->fillFields(['name' => 'test', 'value' => 123]);
        $this->addToAssertionCount(1);
    }

    public function test_title_stored_correctly(): void
    {
        $blockForm = $this->createBlockForm();

        $blockForm->title('Section Title');

        $ref = new \ReflectionProperty($blockForm, 'title');
        $ref->setAccessible(true);
        $this->assertSame('Section Title', $ref->getValue($blockForm));
    }

    public function test_show_footer_enables_buttons(): void
    {
        $blockForm = $this->createBlockForm();

        $blockForm->showFooter();

        $refAjax = new \ReflectionProperty($blockForm, 'ajax');
        $refAjax->setAccessible(true);
        $this->assertTrue($refAjax->getValue($blockForm));

        $refButtons = new \ReflectionProperty($blockForm, 'buttons');
        $refButtons->setAccessible(true);
        $buttons = $refButtons->getValue($blockForm);

        $this->assertTrue($buttons['submit']);
        $this->assertTrue($buttons['reset']);
    }
}
