<?php

namespace Dcat\Admin\Tests\Unit\Widgets\Metrics;

use Dcat\Admin\Tests\TestCase;
use Dcat\Admin\Widgets\ApexCharts\Chart;
use Dcat\Admin\Widgets\Metrics\Card;
use ReflectionProperty;

class CardTest extends TestCase
{
    protected function getProtectedProperty(object $object, string $property)
    {
        $reflection = new ReflectionProperty($object, $property);
        $reflection->setAccessible(true);

        return $reflection->getValue($object);
    }

    protected function setProtectedProperty(object $object, string $property, mixed $value): void
    {
        $reflection = new ReflectionProperty($object, $property);
        $reflection->setAccessible(true);
        $reflection->setValue($object, $value);
    }

    public function test_constructor_sets_title_and_icon(): void
    {
        $card = new Card('Total Users', 'fa-users');

        $this->assertEquals('Total Users', $this->getProtectedProperty($card, 'title'));
        $this->assertEquals('fa-users', $this->getProtectedProperty($card, 'icon'));
    }

    public function test_constructor_with_null_arguments(): void
    {
        $card = new Card;

        $this->assertNull($this->getProtectedProperty($card, 'title'));
        $this->assertNull($this->getProtectedProperty($card, 'icon'));
    }

    public function test_title_method(): void
    {
        $card = new Card;
        $result = $card->title('Revenue');

        $this->assertSame($card, $result);
        $this->assertEquals('Revenue', $this->getProtectedProperty($card, 'title'));
    }

    public function test_sub_title_method(): void
    {
        $card = new Card;
        $result = $card->subTitle('Monthly');

        $this->assertSame($card, $result);
        $this->assertEquals('Monthly', $this->getProtectedProperty($card, 'subTitle'));
    }

    public function test_icon_method(): void
    {
        $card = new Card;
        $result = $card->icon('fa-chart-bar');

        $this->assertSame($card, $result);
        $this->assertEquals('fa-chart-bar', $this->getProtectedProperty($card, 'icon'));
    }

    public function test_header_method(): void
    {
        $card = new Card;
        $result = $card->header('<strong>Header</strong>');

        $this->assertSame($card, $result);
        $this->assertEquals('<strong>Header</strong>', $card->renderHeader());
    }

    public function test_content_method(): void
    {
        $card = new Card;
        $result = $card->content('<p>Content here</p>');

        $this->assertSame($card, $result);
        $this->assertEquals('<p>Content here</p>', $card->renderContent());
    }

    public function test_style_method(): void
    {
        $card = new Card;
        $result = $card->style('danger');

        $this->assertSame($card, $result);
        $this->assertEquals('danger', $this->getProtectedProperty($card, 'style'));
    }

    public function test_default_style_is_primary(): void
    {
        $card = new Card;

        $this->assertEquals('primary', $this->getProtectedProperty($card, 'style'));
    }

    public function test_dropdown_method(): void
    {
        $card = new Card;
        $items = ['7 days' => 'Last 7 Days', '30 days' => 'Last 30 Days'];
        $result = $card->dropdown($items);

        $this->assertSame($card, $result);
        $this->assertEquals($items, $this->getProtectedProperty($card, 'dropdown'));
    }

    public function test_dropdown_empty_array(): void
    {
        $card = new Card;
        $card->dropdown([]);

        $this->assertEquals([], $this->getProtectedProperty($card, 'dropdown'));
    }

    public function test_height_method(): void
    {
        $card = new Card;
        $result = $card->height(200);

        $this->assertSame($card, $result);
        $this->assertEquals(200, $this->getProtectedProperty($card, 'height'));
    }

    public function test_height_accepts_string(): void
    {
        $card = new Card;
        $card->height('auto');

        $this->assertEquals('auto', $this->getProtectedProperty($card, 'height'));
    }

    public function test_default_height(): void
    {
        $card = new Card;

        $this->assertEquals(165, $this->getProtectedProperty($card, 'height'));
    }

    public function test_chart_height_method(): void
    {
        $card = new Card;
        $result = $card->chartHeight(120);

        $this->assertSame($card, $result);
        $this->assertEquals(120, $this->getProtectedProperty($card, 'chartHeight'));
    }

    public function test_chart_height_creates_chart(): void
    {
        $card = new Card;
        $card->chartHeight(120);

        $chart = $this->getProtectedProperty($card, 'chart');
        $this->assertNotNull($chart);
        $this->assertInstanceOf(Chart::class, $chart);
    }

    public function test_chart_margin_top(): void
    {
        $card = new Card;
        $result = $card->chartMarginTop(10);

        $this->assertSame($card, $result);
        $this->assertEquals(10, $this->getProtectedProperty($card, 'chartMarginTop'));
    }

    public function test_chart_margin_bottom(): void
    {
        $card = new Card;
        $result = $card->chartMarginBottom(15);

        $this->assertSame($card, $result);
        $this->assertEquals(15, $this->getProtectedProperty($card, 'chartMarginBottom'));
    }

    public function test_default_chart_margin_right(): void
    {
        $card = new Card;

        $this->assertEquals(1, $this->getProtectedProperty($card, 'chartMarginRight'));
    }

    public function test_chart_labels(): void
    {
        $card = new Card;
        $result = $card->chartLabels(['Jan', 'Feb', 'Mar']);

        $this->assertSame($card, $result);

        $options = $this->getProtectedProperty($card, 'chartOptions');
        $this->assertEquals(['Jan', 'Feb', 'Mar'], $options['labels']);
    }

    public function test_chart_labels_string_converted_to_array(): void
    {
        $card = new Card;
        $card->chartLabels('Total');

        $options = $this->getProtectedProperty($card, 'chartOptions');
        $this->assertEquals(['Total'], $options['labels']);
    }

    public function test_chart_colors(): void
    {
        $card = new Card;
        $result = $card->chartColors(['#ff0000', '#00ff00']);

        $this->assertSame($card, $result);

        $options = $this->getProtectedProperty($card, 'chartOptions');
        $this->assertEquals(['#ff0000', '#00ff00'], $options['colors']);
    }

    public function test_chart_colors_string_converted_to_array(): void
    {
        $card = new Card;
        $card->chartColors('#ff0000');

        $options = $this->getProtectedProperty($card, 'chartOptions');
        $this->assertEquals(['#ff0000'], $options['colors']);
    }

    public function test_chart_option_sets_nested_value(): void
    {
        $card = new Card;
        $result = $card->chartOption('chart.type', 'line');

        $this->assertSame($card, $result);

        $options = $this->getProtectedProperty($card, 'chartOptions');
        $this->assertEquals('line', $options['chart']['type']);
    }

    public function test_chart_option_deeply_nested(): void
    {
        $card = new Card;
        $card->chartOption('plotOptions.radialBar.hollow.size', '65%');

        $options = $this->getProtectedProperty($card, 'chartOptions');
        $this->assertEquals('65%', $options['plotOptions']['radialBar']['hollow']['size']);
    }

    public function test_chart_with_array_options(): void
    {
        $card = new Card;
        $result = $card->chart(['chart' => ['type' => 'bar']]);

        $this->assertSame($card, $result);

        $options = $this->getProtectedProperty($card, 'chartOptions');
        $this->assertEquals('bar', $options['chart']['type']);
    }

    public function test_chart_with_closure(): void
    {
        $card = new Card;
        $callback = function ($chart) {};
        $result = $card->chart($callback);

        $this->assertSame($card, $result);
        $this->assertSame($callback, $this->getProtectedProperty($card, 'chartCallback'));
    }

    public function test_chart_array_merges_with_existing(): void
    {
        $card = new Card;
        $card->chart(['chart' => ['type' => 'bar']]);
        $card->chart(['chart' => ['height' => 200]]);

        $options = $this->getProtectedProperty($card, 'chartOptions');
        $this->assertEquals(200, $options['chart']['height']);
    }

    public function test_use_chart_returns_chart_instance(): void
    {
        $card = new Card;
        $chart = $card->useChart();

        $this->assertInstanceOf(Chart::class, $chart);
    }

    public function test_use_chart_returns_same_instance(): void
    {
        $card = new Card;
        $chart1 = $card->useChart();
        $chart2 = $card->useChart();

        $this->assertSame($chart1, $chart2);
    }

    public function test_render_header_returns_empty_by_default(): void
    {
        $card = new Card;

        $this->assertEquals('', $card->renderHeader());
    }

    public function test_render_content_returns_empty_by_default(): void
    {
        $card = new Card;

        $this->assertEquals('', $card->renderContent());
    }

    public function test_render_chart_returns_empty_without_chart(): void
    {
        $card = new Card;

        $this->setProtectedProperty($card, 'chart', null);

        $this->assertEquals('', $card->renderChart());
    }

    public function test_init_sets_id_with_prefix(): void
    {
        $card = new Card;

        $id = $card->id();
        $this->assertStringStartsWith('metric-card-', $id);
    }

    public function test_each_instance_gets_unique_id(): void
    {
        $card1 = new Card;
        $card2 = new Card;

        $this->assertNotEquals($card1->id(), $card2->id());
    }

    public function test_static_make(): void
    {
        $card = Card::make('My Title', 'fa-star');

        $this->assertInstanceOf(Card::class, $card);
        $this->assertEquals('My Title', $this->getProtectedProperty($card, 'title'));
        $this->assertEquals('fa-star', $this->getProtectedProperty($card, 'icon'));
    }

    public function test_method_chaining(): void
    {
        $card = (new Card)
            ->title('Users')
            ->subTitle('Active')
            ->icon('fa-users')
            ->style('success')
            ->height(300)
            ->header('<b>Header</b>')
            ->content('<p>Body</p>')
            ->dropdown(['7d' => '7 Days']);

        $this->assertEquals('Users', $this->getProtectedProperty($card, 'title'));
        $this->assertEquals('Active', $this->getProtectedProperty($card, 'subTitle'));
        $this->assertEquals('fa-users', $this->getProtectedProperty($card, 'icon'));
        $this->assertEquals('success', $this->getProtectedProperty($card, 'style'));
        $this->assertEquals(300, $this->getProtectedProperty($card, 'height'));
        $this->assertEquals('<b>Header</b>', $card->renderHeader());
        $this->assertEquals('<p>Body</p>', $card->renderContent());
    }

    public function test_default_chart_options_empty(): void
    {
        $card = new Card;

        $options = $this->getProtectedProperty($card, 'chartOptions');
        $this->assertEquals([], $options);
    }

    public function test_value_result_without_chart(): void
    {
        $card = new Card;
        $card->header('Test Header');
        $card->content('Test Content');

        $this->setProtectedProperty($card, 'chart', null);

        $result = $card->valueResult();

        $this->assertEquals(1, $result['status']);
        $this->assertEquals('Test Header', $result['header']);
        $this->assertEquals('Test Content', $result['content']);
    }
}
