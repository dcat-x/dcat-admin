<?php

namespace Dcat\Admin\Tests\Unit\Widgets;

use Dcat\Admin\Tests\TestCase;
use Dcat\Admin\Widgets\Modal;

class ModalTest extends TestCase
{
    public function test_constructor_creates_instance(): void
    {
        $modal = new Modal('Title', 'Content');
        $this->assertInstanceOf(Modal::class, $modal);
    }

    public function test_title_sets_value(): void
    {
        $modal = new Modal;
        $result = $modal->title('My Title');
        $this->assertSame($modal, $result);
        $ref = new \ReflectionProperty($modal, 'title');
        $ref->setAccessible(true);
        $this->assertEquals('My Title', $ref->getValue($modal));
    }

    public function test_content_sets_string(): void
    {
        $modal = new Modal;
        $result = $modal->content('My Content');
        $this->assertSame($modal, $result);
        $ref = new \ReflectionProperty($modal, 'content');
        $ref->setAccessible(true);
        $this->assertEquals('My Content', $ref->getValue($modal));
    }

    public function test_body_is_alias_for_content(): void
    {
        $modal = new Modal;
        $modal->body('Body Content');
        $ref = new \ReflectionProperty($modal, 'content');
        $ref->setAccessible(true);
        $this->assertEquals('Body Content', $ref->getValue($modal));
    }

    public function test_footer_sets_value(): void
    {
        $modal = new Modal;
        $result = $modal->footer('Footer Content');
        $this->assertSame($modal, $result);
        $ref = new \ReflectionProperty($modal, 'footer');
        $ref->setAccessible(true);
        $this->assertEquals('Footer Content', $ref->getValue($modal));
    }

    public function test_button_sets_value(): void
    {
        $modal = new Modal;
        $result = $modal->button('Click Me');
        $this->assertSame($modal, $result);
        $ref = new \ReflectionProperty($modal, 'button');
        $ref->setAccessible(true);
        $this->assertEquals('Click Me', $ref->getValue($modal));
    }

    public function test_size_sets_value(): void
    {
        $modal = new Modal;
        $result = $modal->size('lg');
        $this->assertSame($modal, $result);
        $ref = new \ReflectionProperty($modal, 'size');
        $ref->setAccessible(true);
        $this->assertEquals('lg', $ref->getValue($modal));
    }

    public function test_sm_sets_size(): void
    {
        $modal = new Modal;
        $modal->sm();
        $ref = new \ReflectionProperty($modal, 'size');
        $ref->setAccessible(true);
        $this->assertEquals('sm', $ref->getValue($modal));
    }

    public function test_lg_sets_size(): void
    {
        $modal = new Modal;
        $modal->lg();
        $ref = new \ReflectionProperty($modal, 'size');
        $ref->setAccessible(true);
        $this->assertEquals('lg', $ref->getValue($modal));
    }

    public function test_xl_sets_size(): void
    {
        $modal = new Modal;
        $modal->xl();
        $ref = new \ReflectionProperty($modal, 'size');
        $ref->setAccessible(true);
        $this->assertEquals('xl', $ref->getValue($modal));
    }

    public function test_centered(): void
    {
        $modal = new Modal;
        $result = $modal->centered();
        $this->assertSame($modal, $result);
        $ref = new \ReflectionProperty($modal, 'centered');
        $ref->setAccessible(true);
        $this->assertEquals('modal-dialog-centered', $ref->getValue($modal));
    }

    public function test_centered_false(): void
    {
        $modal = new Modal;
        $modal->centered(false);
        $ref = new \ReflectionProperty($modal, 'centered');
        $ref->setAccessible(true);
        $this->assertEquals('', $ref->getValue($modal));
    }

    public function test_scrollable(): void
    {
        $modal = new Modal;
        $modal->scrollable();
        $ref = new \ReflectionProperty($modal, 'scrollable');
        $ref->setAccessible(true);
        $this->assertEquals('modal-dialog-scrollable', $ref->getValue($modal));
    }

    public function test_delay_sets_value(): void
    {
        $modal = new Modal;
        $result = $modal->delay(500);
        $this->assertSame($modal, $result);
        $ref = new \ReflectionProperty($modal, 'delay');
        $ref->setAccessible(true);
        $this->assertEquals(500, $ref->getValue($modal));
    }

    public function test_join_sets_value(): void
    {
        $modal = new Modal;
        $result = $modal->join();
        $this->assertSame($modal, $result);
        $ref = new \ReflectionProperty($modal, 'join');
        $ref->setAccessible(true);
        $this->assertTrue($ref->getValue($modal));
    }

    public function test_on_event(): void
    {
        $modal = new Modal;
        $result = $modal->on('show.bs.modal', 'console.log("show")');
        $this->assertSame($modal, $result);
        $ref = new \ReflectionProperty($modal, 'events');
        $ref->setAccessible(true);
        $events = $ref->getValue($modal);
        $this->assertCount(1, $events);
        $this->assertEquals('show.bs.modal', $events[0]['event']);
    }

    public function test_on_show_registers_event(): void
    {
        $modal = new Modal;
        $modal->onShow('alert(1)');
        $ref = new \ReflectionProperty($modal, 'events');
        $ref->setAccessible(true);
        $events = $ref->getValue($modal);
        $this->assertEquals('show.bs.modal', $events[0]['event']);
    }

    public function test_on_hidden_registers_event(): void
    {
        $modal = new Modal;
        $modal->onHidden('cleanup()');
        $ref = new \ReflectionProperty($modal, 'events');
        $ref->setAccessible(true);
        $events = $ref->getValue($modal);
        $this->assertEquals('hidden.bs.modal', $events[0]['event']);
    }

    public function test_html_output_contains_modal_structure(): void
    {
        $modal = new Modal('Test Title', 'Test Content');
        $modal->size('lg');
        $html = $modal->html();
        $this->assertStringContainsString('modal-dialog', $html);
        $this->assertStringContainsString('modal-lg', $html);
        $this->assertStringContainsString('Test Title', $html);
        $this->assertStringContainsString('Test Content', $html);
    }

    public function test_default_size_empty(): void
    {
        $modal = new Modal;
        $ref = new \ReflectionProperty($modal, 'size');
        $ref->setAccessible(true);
        $this->assertEquals('', $ref->getValue($modal));
    }
}
