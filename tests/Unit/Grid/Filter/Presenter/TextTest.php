<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Grid\Filter\Presenter;

use Dcat\Admin\Grid\Filter\AbstractFilter;
use Dcat\Admin\Grid\Filter\Presenter\Text;
use Dcat\Admin\Tests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use ReflectionProperty;

class TextTest extends TestCase
{
    protected function makeText(string $placeholder = ''): Text
    {
        return new Text($placeholder);
    }

    protected function attachFilter(Text $text): void
    {
        $parentFilter = $this->createMock(\Dcat\Admin\Grid\Filter::class);
        $parentFilter->method('filterID')->willReturn('filter_id');

        $filter = $this->createMock(AbstractFilter::class);
        $filter->method('column')->willReturn('test_column');
        $filter->method('getId')->willReturn('test_id');
        $filter->method('parent')->willReturn($parentFilter);
        $filter->group = null;

        $text->setParent($filter);
    }

    public function test_constructor_sets_placeholder(): void
    {
        $text = $this->makeText('Enter name...');

        $ref = new ReflectionProperty($text, 'placeholder');
        $ref->setAccessible(true);

        $this->assertSame('Enter name...', $ref->getValue($text));
    }

    public function test_constructor_with_empty_placeholder(): void
    {
        $text = $this->makeText();

        $ref = new ReflectionProperty($text, 'placeholder');
        $ref->setAccessible(true);

        $this->assertSame('', $ref->getValue($text));
    }

    public function test_placeholder_sets_and_returns_self(): void
    {
        $text = $this->makeText();

        $result = $text->placeholder('Search here...');

        $this->assertSame($text, $result);

        $ref = new ReflectionProperty($text, 'placeholder');
        $ref->setAccessible(true);

        $this->assertSame('Search here...', $ref->getValue($text));
    }

    public function test_default_type_is_text(): void
    {
        $text = $this->makeText();

        $ref = new ReflectionProperty($text, 'type');
        $ref->setAccessible(true);

        $this->assertSame('text', $ref->getValue($text));
    }

    public function test_default_icon_is_pencil(): void
    {
        $text = $this->makeText();

        $ref = new ReflectionProperty($text, 'icon');
        $ref->setAccessible(true);

        $this->assertSame('pencil', $ref->getValue($text));
    }

    #[DataProvider('defaultVariableKeyProvider')]
    public function test_default_variables_returns_expected_keys(string $key): void
    {
        $text = $this->makeText('My placeholder');
        $this->attachFilter($text);

        $vars = $text->defaultVariables();

        $this->assertContains($key, array_keys($vars));
        $this->assertSame('My placeholder', $vars['placeholder']);
        $this->assertSame('pencil', $vars['icon']);
        $this->assertSame('text', $vars['type']);
    }

    public function test_view_returns_text_view(): void
    {
        $text = $this->makeText();

        $this->assertSame('admin::filter.text', $text->view());
    }

    public function test_url_sets_icon_to_internet_explorer(): void
    {
        $text = $this->makeText();
        $this->attachFilter($text);

        $text->url();

        $ref = new ReflectionProperty($text, 'icon');
        $ref->setAccessible(true);

        $this->assertSame('internet-explorer', $ref->getValue($text));
    }

    public function test_email_sets_icon_to_envelope(): void
    {
        $text = $this->makeText();
        $this->attachFilter($text);

        $text->email();

        $ref = new ReflectionProperty($text, 'icon');
        $ref->setAccessible(true);

        $this->assertSame('envelope', $ref->getValue($text));
    }

    public static function defaultVariableKeyProvider(): array
    {
        return [
            ['placeholder'],
            ['icon'],
            ['type'],
            ['group'],
        ];
    }
}
