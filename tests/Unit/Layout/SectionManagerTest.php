<?php

namespace Dcat\Admin\Tests\Unit\Layout;

use Dcat\Admin\Layout\SectionManager;
use Dcat\Admin\Tests\TestCase;

class SectionManagerTest extends TestCase
{
    private SectionManager $manager;

    protected function setUp(): void
    {
        parent::setUp();
        $this->manager = new SectionManager;
    }

    public function test_has_section_returns_false_for_nonexistent_section(): void
    {
        $this->assertFalse($this->manager->hasSection('nonexistent'));
    }

    public function test_inject_registers_section(): void
    {
        $this->manager->inject('header', 'Header Content');

        $this->assertTrue($this->manager->hasSection('header'));
    }

    public function test_inject_multiple_contents_to_same_section(): void
    {
        $this->manager->inject('footer', 'First');
        $this->manager->inject('footer', 'Second');

        $this->assertTrue($this->manager->hasSection('footer'));

        $sections = $this->manager->getSections('footer');
        $this->assertCount(2, $sections);
    }

    public function test_has_section_returns_true_after_injection(): void
    {
        $this->manager->inject('sidebar', 'Sidebar Content');

        $this->assertTrue($this->manager->hasSection('sidebar'));
    }

    public function test_yield_content_returns_default_when_section_not_exists(): void
    {
        $result = $this->manager->yieldContent('missing', 'default value');

        $this->assertEquals('default value', $result);
    }

    public function test_yield_content_returns_injected_content(): void
    {
        $this->manager->inject('title', 'My Title');

        $result = $this->manager->yieldContent('title');

        $this->assertEquals('My Title', $result);
    }

    public function test_yield_content_with_appended_items(): void
    {
        $this->manager->inject('scripts', 'script1');
        $this->manager->inject('scripts', 'script2');

        $result = $this->manager->yieldContent('scripts');

        $this->assertStringContainsString('script1', $result);
        $this->assertStringContainsString('script2', $result);
    }

    public function test_inject_with_append_false_replaces_content(): void
    {
        $this->manager->inject('content', 'original', true);
        $this->manager->inject('content', 'replacement', false);

        $result = $this->manager->yieldContent('content');

        // When append=false, previous content is cleared
        $this->assertEquals('replacement', $result);
    }

    public function test_flush_sections_clears_all(): void
    {
        $this->manager->inject('header', 'Header');
        $this->manager->inject('footer', 'Footer');

        $this->assertTrue($this->manager->hasSection('header'));
        $this->assertTrue($this->manager->hasSection('footer'));

        $this->manager->flushSections();

        $this->assertFalse($this->manager->hasSection('header'));
        $this->assertFalse($this->manager->hasSection('footer'));
    }

    public function test_get_sections_returns_empty_array_for_nonexistent(): void
    {
        $result = $this->manager->getSections('nonexistent');

        $this->assertEmpty($result);
    }

    public function test_get_sections_returns_sorted_items(): void
    {
        $this->manager->inject('main', 'low priority', true, 5);
        $this->manager->inject('main', 'high priority', true, 20);

        $sections = $this->manager->getSections('main');

        $this->assertCount(2, $sections);
        // Higher priority first (krsort)
        $this->assertEquals('high priority', $sections[0]['value']);
        $this->assertEquals('low priority', $sections[1]['value']);
    }

    public function test_inject_default_stores_default_section(): void
    {
        $this->manager->injectDefault('banner', 'Default Banner');

        $this->assertTrue($this->manager->hasDefaultSection('banner'));
        $this->assertFalse($this->manager->hasSection('banner'));
    }

    public function test_inject_default_is_overridden_by_inject(): void
    {
        $this->manager->injectDefault('banner', 'Default Banner');
        $this->manager->inject('banner', 'Custom Banner');

        $result = $this->manager->yieldContent('banner');

        $this->assertEquals('Custom Banner', $result);
    }

    public function test_inject_default_skipped_when_section_already_exists(): void
    {
        $this->manager->inject('header', 'Existing Header');
        $this->manager->injectDefault('header', 'Default Header');

        $result = $this->manager->yieldContent('header');

        $this->assertEquals('Existing Header', $result);
    }

    public function test_yield_content_uses_default_when_no_regular_section(): void
    {
        $this->manager->injectDefault('notice', 'Default Notice');

        $result = $this->manager->yieldContent('notice');

        $this->assertEquals('Default Notice', $result);
    }

    public function test_has_default_section_returns_false_for_nonexistent(): void
    {
        $this->assertFalse($this->manager->hasDefaultSection('nope'));
    }

    public function test_flush_clears_default_sections_too(): void
    {
        $this->manager->injectDefault('default_sec', 'content');

        $this->assertTrue($this->manager->hasDefaultSection('default_sec'));

        $this->manager->flushSections();

        $this->assertFalse($this->manager->hasDefaultSection('default_sec'));
    }

    public function test_inject_with_priority_ordering(): void
    {
        $this->manager->inject('ordered', 'medium', true, 10);
        $this->manager->inject('ordered', 'low', true, 1);
        $this->manager->inject('ordered', 'high', true, 100);

        $sections = $this->manager->getSections('ordered');

        $this->assertCount(3, $sections);
        $this->assertEquals('high', $sections[0]['value']);
        $this->assertEquals('medium', $sections[1]['value']);
        $this->assertEquals('low', $sections[2]['value']);
    }
}
