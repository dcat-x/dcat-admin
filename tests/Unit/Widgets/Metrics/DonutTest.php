<?php

namespace Dcat\Admin\Tests\Unit\Widgets\Metrics;

use Dcat\Admin\Tests\TestCase;
use Dcat\Admin\Widgets\ApexCharts\Chart;
use Dcat\Admin\Widgets\Metrics\Card;
use Dcat\Admin\Widgets\Metrics\Donut;
use ReflectionProperty;

class DonutTest extends TestCase
{
    protected function getProtectedProperty(object $object, string $property)
    {
        $reflection = new ReflectionProperty($object, $property);
        $reflection->setAccessible(true);

        return $reflection->getValue($object);
    }

    public function test_extends_card(): void
    {
        $donut = new Donut;

        $this->assertInstanceOf(Card::class, $donut);
    }

    public function test_default_chart_height_is_100(): void
    {
        $donut = new Donut;

        $this->assertEquals(100, $this->getProtectedProperty($donut, 'chartHeight'));
    }

    public function test_default_chart_margin_top_is_5(): void
    {
        $donut = new Donut;

        $this->assertEquals(5, $this->getProtectedProperty($donut, 'chartMarginTop'));
    }

    public function test_default_content_width_is_6_6(): void
    {
        $donut = new Donut;

        $this->assertEquals([6, 6], $this->getProtectedProperty($donut, 'contentWidth'));
    }

    public function test_content_width_setter_fluent(): void
    {
        $donut = new Donut;
        $result = $donut->contentWidth(4, 8);

        $this->assertSame($donut, $result);
        $this->assertEquals([4, 8], $this->getProtectedProperty($donut, 'contentWidth'));
    }

    public function test_chart_options_type_is_donut(): void
    {
        $donut = new Donut;

        $options = $this->getProtectedProperty($donut, 'chartOptions');

        $this->assertEquals('donut', $options['chart']['type']);
    }

    public function test_init_creates_chart(): void
    {
        $donut = new Donut;

        $chart = $this->getProtectedProperty($donut, 'chart');
        $this->assertNotNull($chart);
        $this->assertInstanceOf(Chart::class, $chart);
    }
}
