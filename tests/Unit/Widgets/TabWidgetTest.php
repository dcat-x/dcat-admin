<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Widgets;

use Dcat\Admin\Tests\TestCase;
use Dcat\Admin\Widgets\Tab;

class TabWidgetTest extends TestCase
{
    public function test_constructor(): void
    {
        $tab = new Tab;
        $this->assertInstanceOf(Tab::class, $tab);
    }

    public function test_type_constants(): void
    {
        $this->assertSame(1, Tab::TYPE_CONTENT);
        $this->assertSame(2, Tab::TYPE_LINK);
    }

    public function test_add_tab(): void
    {
        $tab = new Tab;
        $result = $tab->add('Tab 1', 'Content 1');
        $this->assertSame($tab, $result);

        $ref = new \ReflectionProperty($tab, 'data');
        $ref->setAccessible(true);
        $data = $ref->getValue($tab);
        $this->assertCount(1, $data['tabs']);
        $this->assertSame('Tab 1', $data['tabs'][0]['title']);
    }

    public function test_add_active_tab(): void
    {
        $tab = new Tab;
        $tab->add('Tab 1', 'Content 1');
        $tab->add('Tab 2', 'Content 2', true);

        $ref = new \ReflectionProperty($tab, 'data');
        $ref->setAccessible(true);
        $data = $ref->getValue($tab);
        $this->assertSame(1, $data['active']);
    }

    public function test_add_tab_with_custom_id(): void
    {
        $tab = new Tab;
        $tab->add('Tab 1', 'Content 1', false, 'custom-id');

        $ref = new \ReflectionProperty($tab, 'data');
        $ref->setAccessible(true);
        $data = $ref->getValue($tab);
        $this->assertSame('custom-id', $data['tabs'][0]['id']);
    }

    public function test_add_link(): void
    {
        $tab = new Tab;
        $result = $tab->addLink('Google', 'https://google.com');
        $this->assertSame($tab, $result);

        $ref = new \ReflectionProperty($tab, 'data');
        $ref->setAccessible(true);
        $data = $ref->getValue($tab);
        $this->assertSame(Tab::TYPE_LINK, $data['tabs'][0]['type']);
        $this->assertSame('https://google.com', $data['tabs'][0]['href']);
    }

    public function test_title(): void
    {
        $tab = new Tab;
        $result = $tab->title('My Tabs');
        $this->assertSame($tab, $result);

        $ref = new \ReflectionProperty($tab, 'data');
        $ref->setAccessible(true);
        $this->assertSame('My Tabs', $ref->getValue($tab)['title']);
    }

    public function test_padding(): void
    {
        $tab = new Tab;
        $result = $tab->padding('10px');
        $this->assertSame($tab, $result);

        $ref = new \ReflectionProperty($tab, 'data');
        $ref->setAccessible(true);
        $this->assertSame('padding:10px', $ref->getValue($tab)['padding']);
    }

    public function test_no_padding(): void
    {
        $tab = new Tab;
        $tab->noPadding();

        $ref = new \ReflectionProperty($tab, 'data');
        $ref->setAccessible(true);
        $this->assertSame('padding:0', $ref->getValue($tab)['padding']);
    }

    public function test_tab_style(): void
    {
        $tab = new Tab;
        $result = $tab->tabStyle('nav-pills');
        $this->assertSame($tab, $result);

        $ref = new \ReflectionProperty($tab, 'data');
        $ref->setAccessible(true);
        $this->assertSame('nav-pills', $ref->getValue($tab)['tabStyle']);
    }

    public function test_default_active_is_zero(): void
    {
        $tab = new Tab;
        $ref = new \ReflectionProperty($tab, 'data');
        $ref->setAccessible(true);
        $this->assertSame(0, $ref->getValue($tab)['active']);
    }
}
