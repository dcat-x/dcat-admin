<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Grid\Concerns;

use Dcat\Admin\Grid\Concerns\HasActions;
use Dcat\Admin\Grid\Displayers\Actions;
use Dcat\Admin\Tests\TestCase;
use Mockery;

class HasActionsTestHelper
{
    use HasActions;

    public $options = [
        'actions_class' => null,
        'actions' => true,
        'edit_button' => true,
        'quick_edit_button' => true,
        'view_button' => true,
        'delete_button' => true,
    ];

    public function option($key, $value = null)
    {
        if ($value !== null) {
            $this->options[$key] = $value;

            return $this;
        }

        return $this->options[$key] ?? null;
    }
}

class HasActionsTest extends TestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }

    protected function createHelper(): HasActionsTestHelper
    {
        return new HasActionsTestHelper;
    }

    public function test_set_action_class(): void
    {
        $helper = $this->createHelper();
        $result = $helper->setActionClass('App\\MyActions');

        $this->assertSame($helper, $result);
        $this->assertSame('App\\MyActions', $helper->options['actions_class']);
    }

    public function test_get_action_class_returns_custom_class(): void
    {
        $helper = $this->createHelper();
        $helper->setActionClass('App\\CustomActions');

        $this->assertSame('App\\CustomActions', $helper->getActionClass());
    }

    public function test_get_action_class_default_fallback(): void
    {
        $helper = $this->createHelper();

        $this->assertSame(Actions::class, $helper->getActionClass());
    }

    public function test_actions_callback_stores_closure(): void
    {
        $helper = $this->createHelper();
        $closure = function () {
            return 'test';
        };

        $result = $helper->actions($closure);

        $this->assertSame($helper, $result);

        $ref = new \ReflectionProperty(HasActionsTestHelper::class, 'actionsCallback');
        $ref->setAccessible(true);
        $callbacks = $ref->getValue($helper);

        $this->assertCount(1, $callbacks);
        $this->assertSame($closure, $callbacks[0]);
    }

    public function test_disable_actions(): void
    {
        $helper = $this->createHelper();
        $helper->disableActions();

        $this->assertFalse($helper->options['actions']);
    }

    public function test_show_actions(): void
    {
        $helper = $this->createHelper();
        $helper->disableActions();
        $helper->showActions();

        $this->assertTrue($helper->options['actions']);
    }

    public function test_disable_edit_button(): void
    {
        $helper = $this->createHelper();
        $result = $helper->disableEditButton();

        $this->assertSame($helper, $result);
        $this->assertFalse($helper->options['edit_button']);
    }

    public function test_show_edit_button(): void
    {
        $helper = $this->createHelper();
        $helper->disableEditButton();
        $helper->showEditButton();

        $this->assertTrue($helper->options['edit_button']);
    }

    public function test_disable_view_button(): void
    {
        $helper = $this->createHelper();
        $result = $helper->disableViewButton();

        $this->assertSame($helper, $result);
        $this->assertFalse($helper->options['view_button']);
    }

    public function test_show_view_button(): void
    {
        $helper = $this->createHelper();
        $helper->disableViewButton();
        $helper->showViewButton();

        $this->assertTrue($helper->options['view_button']);
    }

    public function test_disable_delete_button(): void
    {
        $helper = $this->createHelper();
        $result = $helper->disableDeleteButton();

        $this->assertSame($helper, $result);
        $this->assertFalse($helper->options['delete_button']);
    }

    public function test_show_delete_button(): void
    {
        $helper = $this->createHelper();
        $helper->disableDeleteButton();
        $helper->showDeleteButton();

        $this->assertTrue($helper->options['delete_button']);
    }
}
