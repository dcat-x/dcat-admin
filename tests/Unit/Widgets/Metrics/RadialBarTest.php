<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Widgets\Metrics;

use Dcat\Admin\Tests\TestCase;
use Dcat\Admin\Widgets\ApexCharts\Chart;
use Dcat\Admin\Widgets\Metrics\Card;
use Dcat\Admin\Widgets\Metrics\RadialBar;
use ReflectionProperty;

class RadialBarTest extends TestCase
{
    protected function getProtectedProperty(object $object, string $property)
    {
        $reflection = new ReflectionProperty($object, $property);
        $reflection->setAccessible(true);

        return $reflection->getValue($object);
    }

    public function test_extends_card(): void
    {
        $bar = new RadialBar;

        $this->assertInstanceOf(Card::class, $bar);
    }

    public function test_default_height_is_250(): void
    {
        $bar = new RadialBar;

        $this->assertSame(250, $this->getProtectedProperty($bar, 'height'));
    }

    public function test_default_chart_height_is_150(): void
    {
        $bar = new RadialBar;

        $this->assertSame(150, $this->getProtectedProperty($bar, 'chartHeight'));
    }

    public function test_default_content_width(): void
    {
        $bar = new RadialBar;

        $this->assertSame([2, 10], $this->getProtectedProperty($bar, 'contentWidth'));
    }

    public function test_default_chart_pull_right_is_false(): void
    {
        $bar = new RadialBar;

        $this->assertFalse($this->getProtectedProperty($bar, 'chartPullRight'));
    }

    public function test_footer_method(): void
    {
        $bar = new RadialBar;
        $result = $bar->footer('<span>Footer</span>');

        $this->assertSame($bar, $result);
        $this->assertSame('<span>Footer</span>', $bar->renderFooter());
    }

    public function test_render_footer_empty_by_default(): void
    {
        $bar = new RadialBar;

        $this->assertSame('', $bar->renderFooter());
    }

    public function test_content_width_method(): void
    {
        $bar = new RadialBar;
        $result = $bar->contentWidth(4, 8);

        $this->assertSame($bar, $result);
        $this->assertSame([4, 8], $this->getProtectedProperty($bar, 'contentWidth'));
    }

    public function test_chart_pull_right(): void
    {
        $bar = new RadialBar;
        $result = $bar->chartPullRight();

        $this->assertSame($bar, $result);
        $this->assertTrue($this->getProtectedProperty($bar, 'chartPullRight'));
    }

    public function test_chart_pull_right_false(): void
    {
        $bar = new RadialBar;
        $bar->chartPullRight(true);
        $bar->chartPullRight(false);

        $this->assertFalse($this->getProtectedProperty($bar, 'chartPullRight'));
    }

    public function test_default_chart_options_type(): void
    {
        $bar = new RadialBar;

        $options = $this->getProtectedProperty($bar, 'chartOptions');

        $this->assertSame('radialBar', $options['chart']['type']);
    }

    public function test_default_chart_options_has_plot_options(): void
    {
        $bar = new RadialBar;

        $options = $this->getProtectedProperty($bar, 'chartOptions');

        $this->assertIsArray($options['plotOptions'] ?? null);
        $this->assertIsArray($options['plotOptions']['radialBar'] ?? null);
        $this->assertSame(200, $options['plotOptions']['radialBar']['size']);
    }

    public function test_default_chart_options_has_fill(): void
    {
        $bar = new RadialBar;

        $options = $this->getProtectedProperty($bar, 'chartOptions');

        $this->assertIsArray($options['fill'] ?? null);
        $this->assertSame('gradient', $options['fill']['type']);
        $this->assertSame('dark', $options['fill']['gradient']['shade']);
        $this->assertSame('horizontal', $options['fill']['gradient']['type']);
    }

    public function test_default_chart_options_stroke_dash_array(): void
    {
        $bar = new RadialBar;

        $options = $this->getProtectedProperty($bar, 'chartOptions');

        $this->assertSame(8, $options['stroke']['dashArray']);
    }

    public function test_init_creates_chart(): void
    {
        $bar = new RadialBar;

        $chart = $this->getProtectedProperty($bar, 'chart');
        $this->assertNotNull($chart);
        $this->assertInstanceOf(Chart::class, $chart);
    }

    public function test_static_make(): void
    {
        $bar = RadialBar::make('Revenue', 'fa-dollar');

        $this->assertInstanceOf(RadialBar::class, $bar);
        $this->assertSame('Revenue', $this->getProtectedProperty($bar, 'title'));
    }
}
