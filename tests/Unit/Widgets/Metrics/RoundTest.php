<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Widgets\Metrics;

use Dcat\Admin\Tests\TestCase;
use Dcat\Admin\Widgets\Metrics\RadialBar;
use Dcat\Admin\Widgets\Metrics\Round;
use ReflectionProperty;

class RoundTest extends TestCase
{
    protected function getProtectedProperty(object $object, string $property)
    {
        $reflection = new ReflectionProperty($object, $property);
        $reflection->setAccessible(true);

        return $reflection->getValue($object);
    }

    public function test_extends_radial_bar(): void
    {
        $round = new Round;

        $this->assertInstanceOf(RadialBar::class, $round);
    }

    public function test_default_height_is_250(): void
    {
        $round = new Round;

        $this->assertSame(250, $this->getProtectedProperty($round, 'height'));
    }

    public function test_default_chart_height_is_210(): void
    {
        $round = new Round;

        $this->assertSame(210, $this->getProtectedProperty($round, 'chartHeight'));
    }

    public function test_default_content_width(): void
    {
        $round = new Round;

        $this->assertSame([5, 7], $this->getProtectedProperty($round, 'contentWidth'));
    }

    public function test_default_chart_margin_top(): void
    {
        $round = new Round;

        $this->assertSame(-10, $this->getProtectedProperty($round, 'chartMarginTop'));
    }

    public function test_default_chart_margin_bottom(): void
    {
        $round = new Round;

        $this->assertSame(-20, $this->getProtectedProperty($round, 'chartMarginBottom'));
    }

    public function test_chart_radial_bar_size(): void
    {
        $round = new Round;
        $result = $round->chartRadialBarSize(120);

        $this->assertSame($round, $result);

        $options = $this->getProtectedProperty($round, 'chartOptions');
        $this->assertSame(120, $options['plotOptions']['radialBar']['size']);
    }

    public function test_chart_radial_bar_margin(): void
    {
        $round = new Round;
        $result = $round->chartRadialBarMargin(20);

        $this->assertSame($round, $result);

        $options = $this->getProtectedProperty($round, 'chartOptions');
        $this->assertSame(20, $options['plotOptions']['radialBar']['track']['margin']);
    }

    public function test_chart_total(): void
    {
        $round = new Round;
        $result = $round->chartTotal('Total', 100);

        $this->assertSame($round, $result);

        $options = $this->getProtectedProperty($round, 'chartOptions');
        $total = $options['plotOptions']['radialBar']['dataLabels']['total'];
        $this->assertTrue($total['show']);
        $this->assertSame('Total', $total['label']);
    }

    public function test_chart_label_name_font_size(): void
    {
        $round = new Round;
        $result = $round->chartLabelNameFontSize('16px');

        $this->assertSame($round, $result);

        $options = $this->getProtectedProperty($round, 'chartOptions');
        $this->assertSame('16px', $options['plotOptions']['radialBar']['dataLabels']['name']['fontSize']);
    }

    public function test_chart_label_name_offset_y(): void
    {
        $round = new Round;
        $result = $round->chartLabelNameOffsetY(-5);

        $this->assertSame($round, $result);

        $options = $this->getProtectedProperty($round, 'chartOptions');
        $this->assertSame(-5, $options['plotOptions']['radialBar']['dataLabels']['name']['offsetY']);
    }

    public function test_chart_label_value_font_size(): void
    {
        $round = new Round;
        $result = $round->chartLabelValueFontSize('14px');

        $this->assertSame($round, $result);

        $options = $this->getProtectedProperty($round, 'chartOptions');
        $this->assertSame('14px', $options['plotOptions']['radialBar']['dataLabels']['value']['fontSize']);
    }

    public function test_chart_label_value_offset_y(): void
    {
        $round = new Round;
        $result = $round->chartLabelValueOffsetY(10);

        $this->assertSame($round, $result);

        $options = $this->getProtectedProperty($round, 'chartOptions');
        $this->assertSame(10, $options['plotOptions']['radialBar']['dataLabels']['value']['offsetY']);
    }

    public function test_default_chart_options_type(): void
    {
        $round = new Round;

        $options = $this->getProtectedProperty($round, 'chartOptions');
        $this->assertSame('radialBar', $options['chart']['type']);
    }

    public function test_default_chart_options_stroke(): void
    {
        $round = new Round;

        $options = $this->getProtectedProperty($round, 'chartOptions');
        $this->assertSame('round', $options['stroke']['lineCap']);
    }

    public function test_default_chart_options_hollow_size(): void
    {
        $round = new Round;

        $options = $this->getProtectedProperty($round, 'chartOptions');
        $this->assertSame('20%', $options['plotOptions']['radialBar']['hollow']['size']);
    }

    public function test_default_chart_options_track_stroke_width(): void
    {
        $round = new Round;

        $options = $this->getProtectedProperty($round, 'chartOptions');
        $this->assertSame('100%', $options['plotOptions']['radialBar']['track']['strokeWidth']);
    }

    public function test_default_options_array(): void
    {
        $round = new Round;
        $options = $round->getOptions();

        $this->assertArrayContainsKeys(['icon', 'title', 'header', 'content', 'footer', 'dropdown'], $options);
    }

    public function test_static_make(): void
    {
        $round = Round::make('Stats');

        $this->assertInstanceOf(Round::class, $round);
    }

    private function assertArrayContainsKeys(array $expectedKeys, array $actual): void
    {
        $keys = array_keys($actual);

        foreach ($expectedKeys as $key) {
            $this->assertContains($key, $keys);
        }
    }
}
