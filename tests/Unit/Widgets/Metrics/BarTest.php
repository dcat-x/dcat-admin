<?php

namespace Dcat\Admin\Tests\Unit\Widgets\Metrics;

use Dcat\Admin\Tests\TestCase;
use Dcat\Admin\Widgets\ApexCharts\Chart;
use Dcat\Admin\Widgets\Metrics\Bar;
use Dcat\Admin\Widgets\Metrics\RadialBar;
use ReflectionProperty;

class BarTest extends TestCase
{
    protected function getProtectedProperty(object $object, string $property)
    {
        $reflection = new ReflectionProperty($object, $property);
        $reflection->setAccessible(true);

        return $reflection->getValue($object);
    }

    public function test_extends_radial_bar(): void
    {
        $bar = new Bar;

        $this->assertInstanceOf(RadialBar::class, $bar);
    }

    public function test_default_content_width_is_4_8(): void
    {
        $bar = new Bar;

        $this->assertEquals([4, 8], $this->getProtectedProperty($bar, 'contentWidth'));
    }

    public function test_default_chart_height_is_180(): void
    {
        $bar = new Bar;

        $this->assertEquals(180, $this->getProtectedProperty($bar, 'chartHeight'));
    }

    public function test_default_chart_pull_right_is_true(): void
    {
        $bar = new Bar;

        $this->assertTrue($this->getProtectedProperty($bar, 'chartPullRight'));
    }

    public function test_chart_options_type_is_bar(): void
    {
        $bar = new Bar;

        $options = $this->getProtectedProperty($bar, 'chartOptions');

        $this->assertEquals('bar', $options['chart']['type']);
    }

    public function test_chart_options_sparkline_enabled(): void
    {
        $bar = new Bar;

        $options = $this->getProtectedProperty($bar, 'chartOptions');

        $this->assertTrue($options['chart']['sparkline']['enabled']);
    }

    public function test_chart_bar_column_width_fluent(): void
    {
        $bar = new Bar;
        $result = $bar->chartBarColumnWidth('50%');

        $this->assertSame($bar, $result);
    }

    public function test_init_creates_chart(): void
    {
        $bar = new Bar;

        $chart = $this->getProtectedProperty($bar, 'chart');
        $this->assertNotNull($chart);
        $this->assertInstanceOf(Chart::class, $chart);
    }
}
