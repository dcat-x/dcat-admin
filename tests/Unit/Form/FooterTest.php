<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Form;

use Dcat\Admin\Form\Builder;
use Dcat\Admin\Form\Footer;
use Dcat\Admin\Tests\TestCase;
use Mockery;

class FooterTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    protected function createFooter(): Footer
    {
        $builder = Mockery::mock(Builder::class);

        return new Footer($builder);
    }

    public function test_constructor_creates_instance(): void
    {
        $footer = $this->createFooter();
        $this->assertInstanceOf(Footer::class, $footer);
    }

    public function test_default_buttons(): void
    {
        $footer = $this->createFooter();
        $ref = new \ReflectionProperty($footer, 'buttons');
        $ref->setAccessible(true);
        $buttons = $ref->getValue($footer);

        $this->assertTrue($buttons['reset']);
        $this->assertTrue($buttons['submit']);
    }

    public function test_default_checkboxes(): void
    {
        $footer = $this->createFooter();
        $ref = new \ReflectionProperty($footer, 'checkboxes');
        $ref->setAccessible(true);
        $checkboxes = $ref->getValue($footer);

        $this->assertTrue($checkboxes['view']);
        $this->assertTrue($checkboxes['continue_editing']);
        $this->assertTrue($checkboxes['continue_creating']);
    }

    public function test_default_checkeds_all_false(): void
    {
        $footer = $this->createFooter();
        $ref = new \ReflectionProperty($footer, 'defaultcheckeds');
        $ref->setAccessible(true);
        $checkeds = $ref->getValue($footer);

        $this->assertFalse($checkeds['view']);
        $this->assertFalse($checkeds['continue_editing']);
        $this->assertFalse($checkeds['continue_creating']);
    }

    public function test_disable_reset(): void
    {
        $footer = $this->createFooter();
        $result = $footer->disableReset();

        $this->assertSame($footer, $result);

        $ref = new \ReflectionProperty($footer, 'buttons');
        $ref->setAccessible(true);
        $this->assertFalse($ref->getValue($footer)['reset']);
    }

    public function test_disable_submit(): void
    {
        $footer = $this->createFooter();
        $footer->disableSubmit();

        $ref = new \ReflectionProperty($footer, 'buttons');
        $ref->setAccessible(true);
        $this->assertFalse($ref->getValue($footer)['submit']);
    }

    public function test_disable_reset_with_false_re_enables(): void
    {
        $footer = $this->createFooter();
        $footer->disableReset();
        $footer->disableReset(false);

        $ref = new \ReflectionProperty($footer, 'buttons');
        $ref->setAccessible(true);
        $this->assertTrue($ref->getValue($footer)['reset']);
    }

    public function test_disable_view_check(): void
    {
        $footer = $this->createFooter();
        $footer->disableViewCheck();

        $ref = new \ReflectionProperty($footer, 'checkboxes');
        $ref->setAccessible(true);
        $this->assertFalse($ref->getValue($footer)['view']);
    }

    public function test_disable_editing_check(): void
    {
        $footer = $this->createFooter();
        $footer->disableEditingCheck();

        $ref = new \ReflectionProperty($footer, 'checkboxes');
        $ref->setAccessible(true);
        $this->assertFalse($ref->getValue($footer)['continue_editing']);
    }

    public function test_disable_creating_check(): void
    {
        $footer = $this->createFooter();
        $footer->disableCreatingCheck();

        $ref = new \ReflectionProperty($footer, 'checkboxes');
        $ref->setAccessible(true);
        $this->assertFalse($ref->getValue($footer)['continue_creating']);
    }

    public function test_default_view_checked(): void
    {
        $footer = $this->createFooter();
        $result = $footer->defaultViewChecked();

        $this->assertSame($footer, $result);

        $ref = new \ReflectionProperty($footer, 'defaultcheckeds');
        $ref->setAccessible(true);
        $this->assertTrue($ref->getValue($footer)['view']);
    }

    public function test_default_editing_checked(): void
    {
        $footer = $this->createFooter();
        $footer->defaultEditingChecked();

        $ref = new \ReflectionProperty($footer, 'defaultcheckeds');
        $ref->setAccessible(true);
        $this->assertTrue($ref->getValue($footer)['continue_editing']);
    }

    public function test_default_creating_checked(): void
    {
        $footer = $this->createFooter();
        $footer->defaultCreatingChecked();

        $ref = new \ReflectionProperty($footer, 'defaultcheckeds');
        $ref->setAccessible(true);
        $this->assertTrue($ref->getValue($footer)['continue_creating']);
    }

    public function test_view_sets_custom_view(): void
    {
        $footer = $this->createFooter();
        $footer->view('custom.view', ['key' => 'value']);

        $refView = new \ReflectionProperty($footer, 'view');
        $refView->setAccessible(true);
        $refData = new \ReflectionProperty($footer, 'data');
        $refData->setAccessible(true);

        $this->assertSame('custom.view', $refView->getValue($footer));
        $this->assertSame(['key' => 'value'], $refData->getValue($footer));
    }
}
