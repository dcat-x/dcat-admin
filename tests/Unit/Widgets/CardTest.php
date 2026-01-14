<?php

namespace Dcat\Admin\Tests\Unit\Widgets;

use Dcat\Admin\Tests\TestCase;
use Dcat\Admin\Widgets\Card;

class CardTest extends TestCase
{
    public function test_card_creation(): void
    {
        $card = new Card('Title', 'Content');
        $this->assertInstanceOf(Card::class, $card);
    }

    public function test_card_content_only(): void
    {
        $card = new Card('Only Content');

        $variables = $card->defaultVariables();
        $this->assertEquals('Only Content', $variables['content']);
        $this->assertEquals('', $variables['title']);
    }

    public function test_card_title_and_content(): void
    {
        $card = new Card('Title', 'Content');

        $variables = $card->defaultVariables();
        $this->assertEquals('Title', $variables['title']);
        $this->assertEquals('Content', $variables['content']);
    }

    public function test_card_title(): void
    {
        $card = new Card;
        $card->title('Card Title');

        $variables = $card->defaultVariables();
        $this->assertEquals('Card Title', $variables['title']);
    }

    public function test_card_content(): void
    {
        $card = new Card;
        $card->content('Card Content');

        $variables = $card->defaultVariables();
        $this->assertEquals('Card Content', $variables['content']);
    }

    public function test_card_footer(): void
    {
        $card = new Card('Title', 'Content');
        $card->footer('Footer Content');

        $variables = $card->defaultVariables();
        $this->assertEquals('Footer Content', $variables['footer']);
    }

    public function test_card_tool(): void
    {
        $card = new Card('Title', 'Content');
        $card->tool('<button>Tool 1</button>');
        $card->tool('<button>Tool 2</button>');

        $variables = $card->defaultVariables();
        $this->assertCount(2, $variables['tools']);
        $this->assertContains('<button>Tool 1</button>', $variables['tools']);
        $this->assertContains('<button>Tool 2</button>', $variables['tools']);
    }

    public function test_card_padding(): void
    {
        $card = new Card('Title', 'Content');
        $card->padding('20px');

        $variables = $card->defaultVariables();
        $this->assertEquals('padding:20px', $variables['padding']);
    }

    public function test_card_no_padding(): void
    {
        $card = new Card('Title', 'Content');
        $card->noPadding();

        $variables = $card->defaultVariables();
        $this->assertEquals('padding:0', $variables['padding']);
    }

    public function test_card_with_header_border(): void
    {
        $card = new Card('Title', 'Content');
        $card->withHeaderBorder();

        $variables = $card->defaultVariables();
        $this->assertTrue($variables['divider']);
    }

    public function test_card_static_make(): void
    {
        $card = Card::make('Title', 'Content');
        $this->assertInstanceOf(Card::class, $card);
    }

    public function test_card_chaining(): void
    {
        $card = (new Card)
            ->title('Chained Title')
            ->content('Chained Content')
            ->footer('Chained Footer')
            ->tool('<button>Tool</button>')
            ->padding('10px')
            ->withHeaderBorder();

        $variables = $card->defaultVariables();
        $this->assertEquals('Chained Title', $variables['title']);
        $this->assertEquals('Chained Content', $variables['content']);
        $this->assertEquals('Chained Footer', $variables['footer']);
        $this->assertCount(1, $variables['tools']);
        $this->assertEquals('padding:10px', $variables['padding']);
        $this->assertTrue($variables['divider']);
    }
}
