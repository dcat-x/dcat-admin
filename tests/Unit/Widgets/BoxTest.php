<?php

namespace Dcat\Admin\Tests\Unit\Widgets;

use Dcat\Admin\Tests\TestCase;
use Dcat\Admin\Widgets\Box;

class BoxTest extends TestCase
{
    public function test_box_creation(): void
    {
        $box = new Box('Title', 'Content');
        $this->assertInstanceOf(Box::class, $box);
    }

    public function test_box_default_values(): void
    {
        $box = new Box;

        $variables = $box->defaultVariables();
        $this->assertEquals('Box header', $variables['title']);
        $this->assertEquals('here is the box content.', $variables['content']);
    }

    public function test_box_title(): void
    {
        $box = new Box;
        $box->title('Custom Title');

        $variables = $box->defaultVariables();
        $this->assertEquals('Custom Title', $variables['title']);
    }

    public function test_box_content(): void
    {
        $box = new Box;
        $box->content('Custom Content');

        $variables = $box->defaultVariables();
        $this->assertEquals('Custom Content', $variables['content']);
    }

    public function test_box_padding(): void
    {
        $box = new Box('Title', 'Content');
        $box->padding('15px');

        $variables = $box->defaultVariables();
        $this->assertEquals('padding:15px', $variables['padding']);
    }

    public function test_box_tool(): void
    {
        $box = new Box('Title', 'Content');
        $box->tool('<button>Custom Tool</button>');

        $variables = $box->defaultVariables();
        $this->assertContains('<button>Custom Tool</button>', $variables['tools']);
    }

    public function test_box_collapsable(): void
    {
        $box = new Box('Title', 'Content');
        $box->collapsable();

        $variables = $box->defaultVariables();
        $this->assertCount(1, $variables['tools']);
        $this->assertStringContainsString('data-action="collapse"', $variables['tools'][0]);
    }

    public function test_box_removable(): void
    {
        $box = new Box('Title', 'Content');
        $box->removable();

        $variables = $box->defaultVariables();
        $this->assertCount(1, $variables['tools']);
        $this->assertStringContainsString('data-action="remove"', $variables['tools'][0]);
    }

    public function test_box_solid(): void
    {
        $box = new Box('Title', 'Content');
        $box->solid();

        $this->assertStringContainsString('box-solid', $box->class);
    }

    public function test_box_style(): void
    {
        $box = new Box('Title', 'Content');
        $box->style('primary');

        $this->assertStringContainsString('box-primary', $box->class);
    }

    public function test_box_multiple_styles(): void
    {
        $box = new Box('Title', 'Content');
        $box->style(['primary', 'solid']);

        $this->assertStringContainsString('box-primary', $box->class);
        $this->assertStringContainsString('box-solid', $box->class);
    }

    public function test_box_static_make(): void
    {
        $box = Box::make('Title', 'Content');
        $this->assertInstanceOf(Box::class, $box);
    }

    public function test_box_chaining(): void
    {
        $box = (new Box)
            ->title('Chained Title')
            ->content('Chained Content')
            ->tool('<button>Tool</button>')
            ->collapsable()
            ->removable()
            ->padding('20px');

        $variables = $box->defaultVariables();
        $this->assertEquals('Chained Title', $variables['title']);
        $this->assertEquals('Chained Content', $variables['content']);
        $this->assertCount(3, $variables['tools']); // 1 custom + 1 collapse + 1 remove
        $this->assertEquals('padding:20px', $variables['padding']);
    }
}
