<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Show;

use Dcat\Admin\Show;
use Dcat\Admin\Show\Panel;
use Dcat\Admin\Show\Tools;
use Dcat\Admin\Tests\TestCase;
use Mockery;

class ToolsTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    protected function createTools(): Tools
    {
        $show = new Show(['name' => 'Test', 'email' => 'test@example.com']);
        $panel = new Panel($show);

        return new Tools($panel);
    }

    public function test_constructor_creates_instance(): void
    {
        $tools = $this->createTools();

        $this->assertInstanceOf(Tools::class, $tools);
    }

    public function test_append_returns_this(): void
    {
        $tools = $this->createTools();

        $result = $tools->append('custom tool');

        $this->assertSame($tools, $result);
    }

    public function test_prepend_returns_this(): void
    {
        $tools = $this->createTools();

        $result = $tools->prepend('custom tool');

        $this->assertSame($tools, $result);
    }

    public function test_disable_list(): void
    {
        $tools = $this->createTools();

        $tools->disableList();

        $ref = new \ReflectionProperty(Tools::class, 'showList');
        $ref->setAccessible(true);

        $this->assertFalse($ref->getValue($tools));
    }

    public function test_disable_list_with_false_re_enables(): void
    {
        $tools = $this->createTools();

        $tools->disableList();
        $tools->disableList(false);

        $ref = new \ReflectionProperty(Tools::class, 'showList');
        $ref->setAccessible(true);

        $this->assertTrue($ref->getValue($tools));
    }

    public function test_disable_delete(): void
    {
        $tools = $this->createTools();

        $tools->disableDelete();

        $ref = new \ReflectionProperty(Tools::class, 'showDelete');
        $ref->setAccessible(true);

        $this->assertFalse($ref->getValue($tools));
    }

    public function test_disable_delete_with_false_re_enables(): void
    {
        $tools = $this->createTools();

        $tools->disableDelete();
        $tools->disableDelete(false);

        $ref = new \ReflectionProperty(Tools::class, 'showDelete');
        $ref->setAccessible(true);

        $this->assertTrue($ref->getValue($tools));
    }

    public function test_disable_edit(): void
    {
        $tools = $this->createTools();

        $tools->disableEdit();

        $ref = new \ReflectionProperty(Tools::class, 'showEdit');
        $ref->setAccessible(true);

        $this->assertFalse($ref->getValue($tools));
    }

    public function test_disable_edit_with_false_re_enables(): void
    {
        $tools = $this->createTools();

        $tools->disableEdit();
        $tools->disableEdit(false);

        $ref = new \ReflectionProperty(Tools::class, 'showEdit');
        $ref->setAccessible(true);

        $this->assertTrue($ref->getValue($tools));
    }

    public function test_disable_quick_edit(): void
    {
        $tools = $this->createTools();

        // Default is already false
        $ref = new \ReflectionProperty(Tools::class, 'showQuickEdit');
        $ref->setAccessible(true);

        $this->assertFalse($ref->getValue($tools));

        // disableQuickEdit(true) sets showQuickEdit = false (same as default)
        $tools->disableQuickEdit();

        $this->assertFalse($ref->getValue($tools));
    }

    public function test_show_quick_edit_sets_flags(): void
    {
        $tools = $this->createTools();

        $result = $tools->showQuickEdit();

        $this->assertSame($tools, $result);

        $refQuickEdit = new \ReflectionProperty(Tools::class, 'showQuickEdit');
        $refQuickEdit->setAccessible(true);

        $refEdit = new \ReflectionProperty(Tools::class, 'showEdit');
        $refEdit->setAccessible(true);

        $this->assertTrue($refQuickEdit->getValue($tools));
        $this->assertFalse($refEdit->getValue($tools));
    }

    public function test_show_quick_edit_with_dimensions(): void
    {
        $tools = $this->createTools();

        $tools->showQuickEdit('900px', '500px');

        $ref = new \ReflectionProperty(Tools::class, 'dialogFormDimensions');
        $ref->setAccessible(true);

        $this->assertSame(['900px', '500px'], $ref->getValue($tools));
    }
}
