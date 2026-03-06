<?php

namespace Dcat\Admin\Tests\Unit\Widgets;

use Dcat\Admin\Tests\TestCase;
use Dcat\Admin\Widgets\DialogTable;
use Dcat\Admin\Widgets\Widget;

class DialogTableTest extends TestCase
{
    public function test_dialog_table_extends_widget(): void
    {
        $dialog = new DialogTable;
        $this->assertInstanceOf(Widget::class, $dialog);
    }

    public function test_title_sets_value(): void
    {
        $dialog = new DialogTable;
        $result = $dialog->title('Test Title');

        $this->assertSame($dialog, $result);

        $reflection = new \ReflectionProperty(DialogTable::class, 'title');
        $reflection->setAccessible(true);
        $this->assertSame('Test Title', $reflection->getValue($dialog));
    }

    public function test_width_sets_value(): void
    {
        $dialog = new DialogTable;
        $result = $dialog->width('600px');

        $this->assertSame($dialog, $result);

        $reflection = new \ReflectionProperty(DialogTable::class, 'width');
        $reflection->setAccessible(true);
        $this->assertSame('600px', $reflection->getValue($dialog));
    }

    public function test_default_width(): void
    {
        $dialog = new DialogTable;

        $reflection = new \ReflectionProperty(DialogTable::class, 'width');
        $reflection->setAccessible(true);
        $this->assertSame('825px', $reflection->getValue($dialog));
    }

    public function test_maxmin_sets_value(): void
    {
        $dialog = new DialogTable;
        $result = $dialog->maxmin(false);

        $this->assertSame($dialog, $result);

        $reflection = new \ReflectionProperty(DialogTable::class, 'maxmin');
        $reflection->setAccessible(true);
        $this->assertFalse($reflection->getValue($dialog));
    }

    public function test_resize_sets_value(): void
    {
        $dialog = new DialogTable;
        $result = $dialog->resize(false);

        $this->assertSame($dialog, $result);

        $reflection = new \ReflectionProperty(DialogTable::class, 'resize');
        $reflection->setAccessible(true);
        $this->assertFalse($reflection->getValue($dialog));
    }

    public function test_button_sets_value(): void
    {
        $dialog = new DialogTable;
        $result = $dialog->button('Click me');

        $this->assertSame($dialog, $result);

        $reflection = new \ReflectionProperty(DialogTable::class, 'button');
        $reflection->setAccessible(true);
        $this->assertSame('Click me', $reflection->getValue($dialog));
    }

    public function test_footer_sets_value(): void
    {
        $dialog = new DialogTable;
        $result = $dialog->footer('Footer content');

        $this->assertSame($dialog, $result);

        $reflection = new \ReflectionProperty(DialogTable::class, 'footer');
        $reflection->setAccessible(true);
        $this->assertSame('Footer content', $reflection->getValue($dialog));
    }

    public function test_on_shown_appends_script(): void
    {
        $dialog = new DialogTable;
        $dialog->onShown('alert(1)');

        $reflection = new \ReflectionProperty(DialogTable::class, 'events');
        $reflection->setAccessible(true);
        $events = $reflection->getValue($dialog);

        $this->assertStringContainsString('alert(1)', $events['shown']);
    }

    public function test_on_hidden_appends_script(): void
    {
        $dialog = new DialogTable;
        $dialog->onHidden('cleanup()');

        $reflection = new \ReflectionProperty(DialogTable::class, 'events');
        $reflection->setAccessible(true);
        $events = $reflection->getValue($dialog);

        $this->assertStringContainsString('cleanup()', $events['hidden']);
    }

    public function test_on_load_appends_script(): void
    {
        $dialog = new DialogTable;
        $dialog->onLoad('init()');

        $reflection = new \ReflectionProperty(DialogTable::class, 'events');
        $reflection->setAccessible(true);
        $events = $reflection->getValue($dialog);

        $this->assertStringContainsString('init()', $events['load']);
    }

    public function test_default_events_are_null(): void
    {
        $dialog = new DialogTable;

        $reflection = new \ReflectionProperty(DialogTable::class, 'events');
        $reflection->setAccessible(true);
        $events = $reflection->getValue($dialog);

        $this->assertNull($events['shown']);
        $this->assertNull($events['hidden']);
        $this->assertNull($events['load']);
    }
}
