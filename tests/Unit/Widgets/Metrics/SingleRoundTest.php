<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Widgets\Metrics;

use Dcat\Admin\Tests\TestCase;
use Dcat\Admin\Widgets\Metrics\Round;
use Dcat\Admin\Widgets\Metrics\SingleRound;
use ReflectionProperty;

class SingleRoundTest extends TestCase
{
    protected function getProtectedProperty(object $object, string $property)
    {
        $reflection = new ReflectionProperty($object, $property);
        $reflection->setAccessible(true);

        return $reflection->getValue($object);
    }

    public function test_extends_round(): void
    {
        $single = new SingleRound;

        $this->assertInstanceOf(Round::class, $single);
    }

    public function test_default_chart_margin_bottom_is_5(): void
    {
        $single = new SingleRound;

        $this->assertSame(5, $this->getProtectedProperty($single, 'chartMarginBottom'));
    }

    public function test_chart_options_type_is_radial_bar(): void
    {
        $single = new SingleRound;

        $options = $this->getProtectedProperty($single, 'chartOptions');

        $this->assertSame('radialBar', $options['chart']['type']);
    }

    public function test_chart_options_sparkline_enabled(): void
    {
        $single = new SingleRound;

        $options = $this->getProtectedProperty($single, 'chartOptions');

        $this->assertTrue($options['chart']['sparkline']['enabled']);
    }

    public function test_chart_options_hollow_size_is_74_percent(): void
    {
        $single = new SingleRound;

        $options = $this->getProtectedProperty($single, 'chartOptions');

        $this->assertSame('74%', $options['plotOptions']['radialBar']['hollow']['size']);
    }

    public function test_chart_options_fill_type_is_gradient(): void
    {
        $single = new SingleRound;

        $options = $this->getProtectedProperty($single, 'chartOptions');

        $this->assertSame('gradient', $options['fill']['type']);
    }
}
