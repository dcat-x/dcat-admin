<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Widgets\ApexCharts;

use Dcat\Admin\Tests\TestCase;
use Dcat\Admin\Widgets\ApexCharts\Chart;
use Dcat\Admin\Widgets\Widget;
use Mockery;

class ChartTest extends TestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }

    public function test_extends_widget(): void
    {
        $parents = class_parents(Chart::class);

        $this->assertContains(Widget::class, $parents);
    }

    public function test_constructor_with_selector(): void
    {
        $chart = new Chart('#my-chart');
        $this->assertSame('#my-chart', $chart->selector());
    }

    public function test_constructor_without_selector(): void
    {
        $chart = new Chart;
        $this->assertNull($chart->selector());
    }

    public function test_constructor_with_options_only(): void
    {
        $chart = new Chart(['chart' => ['type' => 'line']]);
        $this->assertNull($chart->selector());
    }

    public function test_selector_getter(): void
    {
        $chart = new Chart;
        $this->assertNull($chart->selector());
    }

    public function test_selector_setter(): void
    {
        $chart = new Chart;
        $result = $chart->selector('#container');
        $this->assertSame($chart, $result);
        $this->assertSame('#container', $chart->selector());
    }

    public function test_title_with_string(): void
    {
        $chart = new Chart;
        $result = $chart->title('My Chart');
        $this->assertSame($chart, $result);

        $ref = new \ReflectionProperty($chart, 'options');
        $ref->setAccessible(true);
        $opts = $ref->getValue($chart);
        $this->assertSame(['text' => 'My Chart'], $opts['title']);
    }

    public function test_title_with_array(): void
    {
        $chart = new Chart;
        $chart->title(['text' => 'Title', 'align' => 'center']);

        $ref = new \ReflectionProperty($chart, 'options');
        $ref->setAccessible(true);
        $opts = $ref->getValue($chart);
        $this->assertSame('center', $opts['title']['align']);
    }

    public function test_series(): void
    {
        $chart = new Chart;
        $result = $chart->series([['name' => 'A', 'data' => [1, 2, 3]]]);
        $this->assertSame($chart, $result);
    }

    public function test_labels(): void
    {
        $chart = new Chart;
        $result = $chart->labels(['Jan', 'Feb']);
        $this->assertSame($chart, $result);
    }

    public function test_colors(): void
    {
        $chart = new Chart;
        $result = $chart->colors(['#FF0000', '#00FF00']);
        $this->assertSame($chart, $result);
    }

    public function test_stroke(): void
    {
        $chart = new Chart;
        $result = $chart->stroke(['width' => 2]);
        $this->assertSame($chart, $result);
    }

    public function test_xaxis(): void
    {
        $chart = new Chart;
        $result = $chart->xaxis(['categories' => ['A', 'B']]);
        $this->assertSame($chart, $result);
    }

    public function test_yaxis(): void
    {
        $chart = new Chart;
        $result = $chart->yaxis(['min' => 0, 'max' => 100]);
        $this->assertSame($chart, $result);
    }

    public function test_tooltip(): void
    {
        $chart = new Chart;
        $result = $chart->tooltip(['enabled' => true]);
        $this->assertSame($chart, $result);
    }

    public function test_fill(): void
    {
        $chart = new Chart;
        $result = $chart->fill(['type' => 'gradient']);
        $this->assertSame($chart, $result);
    }

    public function test_chart_method(): void
    {
        $chart = new Chart;
        $result = $chart->chart(['type' => 'bar']);
        $this->assertSame($chart, $result);
    }

    public function test_data_labels_with_bool(): void
    {
        $chart = new Chart;
        $result = $chart->dataLabels(false);
        $this->assertSame($chart, $result);

        $ref = new \ReflectionProperty($chart, 'options');
        $ref->setAccessible(true);
        $opts = $ref->getValue($chart);
        $this->assertSame(['enabled' => false], $opts['dataLabels']);
    }

    public function test_data_labels_with_array(): void
    {
        $chart = new Chart;
        $chart->dataLabels(['enabled' => true, 'style' => ['fontSize' => '12px']]);

        $ref = new \ReflectionProperty($chart, 'options');
        $ref->setAccessible(true);
        $opts = $ref->getValue($chart);
        $this->assertTrue($opts['dataLabels']['enabled']);
    }

    public function test_js_static_property(): void
    {
        $this->assertContains('@apex-charts', Chart::$js);
    }

    public function test_value_result_returns_array(): void
    {
        $chart = new Chart('#test');
        $result = $chart->valueResult();
        $this->assertIsArray($result);
        $this->assertSame(1, $result['status']);
        $this->assertSame('#test', $result['selector']);
    }

    public function test_generate_id_is_protected(): void
    {
        $ref = new \ReflectionMethod(Chart::class, 'generateId');
        $this->assertTrue($ref->isProtected());
    }
}
