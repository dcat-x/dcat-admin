<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Widgets\Metrics;

use Dcat\Admin\Tests\TestCase;
use Dcat\Admin\Widgets\ApexCharts\Chart;
use Dcat\Admin\Widgets\Metrics\Card;
use Dcat\Admin\Widgets\Metrics\Line;
use ReflectionProperty;

class LineTest extends TestCase
{
    protected function getProtectedProperty(object $object, string $property)
    {
        $reflection = new ReflectionProperty($object, $property);
        $reflection->setAccessible(true);

        return $reflection->getValue($object);
    }

    public function test_extends_card(): void
    {
        $line = new Line;

        $this->assertInstanceOf(Card::class, $line);
    }

    public function test_default_chart_height_is_57(): void
    {
        $line = new Line;

        $this->assertSame(57, $this->getProtectedProperty($line, 'chartHeight'));
    }

    public function test_chart_options_type_is_area(): void
    {
        $line = new Line;

        $options = $this->getProtectedProperty($line, 'chartOptions');

        $this->assertSame('area', $options['chart']['type']);
    }

    public function test_chart_options_sparkline_enabled(): void
    {
        $line = new Line;

        $options = $this->getProtectedProperty($line, 'chartOptions');

        $this->assertTrue($options['chart']['sparkline']['enabled']);
    }

    public function test_default_stroke_curve_is_smooth(): void
    {
        $line = new Line;

        $options = $this->getProtectedProperty($line, 'chartOptions');

        $this->assertSame('smooth', $options['stroke']['curve']);
    }

    public function test_chart_straight_fluent(): void
    {
        $line = new Line;
        $result = $line->chartStraight();

        $this->assertSame($line, $result);
    }

    public function test_chart_straight_sets_stroke_curve(): void
    {
        $line = new Line;
        $line->chartStraight();

        $options = $this->getProtectedProperty($line, 'chartOptions');

        $this->assertSame('straight', $options['stroke']['curve']);
    }

    public function test_chart_smooth_fluent(): void
    {
        $line = new Line;
        $result = $line->chartSmooth();

        $this->assertSame($line, $result);
    }

    public function test_chart_smooth_sets_stroke_curve(): void
    {
        $line = new Line;
        $line->chartStraight();
        $line->chartSmooth();

        $options = $this->getProtectedProperty($line, 'chartOptions');

        $this->assertSame('smooth', $options['stroke']['curve']);
    }

    public function test_init_creates_chart(): void
    {
        $line = new Line;

        $chart = $this->getProtectedProperty($line, 'chart');
        $this->assertNotNull($chart);
        $this->assertInstanceOf(Chart::class, $chart);
    }
}
