<?php

namespace Dcat\Admin\Tests\Unit\Grid\Filter\Presenter;

use Dcat\Admin\Grid\Filter\Presenter\DateRangeQuick;
use Dcat\Admin\Grid\Filter\Presenter\Presenter;
use Dcat\Admin\Tests\TestCase;
use Mockery;

class DateRangeQuickTest extends TestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }

    public function test_extends_presenter(): void
    {
        $this->assertTrue(is_subclass_of(DateRangeQuick::class, Presenter::class));
    }

    public function test_constructor_with_empty_ranges_uses_defaults(): void
    {
        $drq = new DateRangeQuick;
        $ref = new \ReflectionProperty($drq, 'ranges');
        $ref->setAccessible(true);
        $this->assertNotEmpty($ref->getValue($drq));
    }

    public function test_constructor_with_custom_ranges(): void
    {
        $ranges = ['Today' => ['2024-01-01', '2024-01-01']];
        $drq = new DateRangeQuick($ranges);
        $ref = new \ReflectionProperty($drq, 'ranges');
        $ref->setAccessible(true);
        $this->assertSame($ranges, $ref->getValue($drq));
    }

    public function test_options_merges_options(): void
    {
        $drq = new DateRangeQuick;
        $result = $drq->options(['locale' => 'zh-CN']);
        $this->assertSame($drq, $result);

        $ref = new \ReflectionProperty($drq, 'dateOptions');
        $ref->setAccessible(true);
        $opts = $ref->getValue($drq);
        $this->assertSame('zh-CN', $opts['locale']);
    }

    public function test_format_sets_format(): void
    {
        $drq = new DateRangeQuick;
        $result = $drq->format('YYYY/MM/DD');
        $this->assertSame($drq, $result);

        $ref = new \ReflectionProperty($drq, 'dateOptions');
        $ref->setAccessible(true);
        $opts = $ref->getValue($drq);
        $this->assertSame('YYYY/MM/DD', $opts['format']);
    }

    public function test_format_default(): void
    {
        $drq = new DateRangeQuick;
        $drq->format();

        $ref = new \ReflectionProperty($drq, 'dateOptions');
        $ref->setAccessible(true);
        $opts = $ref->getValue($drq);
        $this->assertSame('YYYY-MM-DD', $opts['format']);
    }

    public function test_hide_date_inputs(): void
    {
        $drq = new DateRangeQuick;
        $result = $drq->hideDateInputs();
        $this->assertSame($drq, $result);

        $ref = new \ReflectionProperty($drq, 'showDateInputs');
        $ref->setAccessible(true);
        $this->assertFalse($ref->getValue($drq));
    }

    public function test_show_date_inputs_default_true(): void
    {
        $drq = new DateRangeQuick;
        $ref = new \ReflectionProperty($drq, 'showDateInputs');
        $ref->setAccessible(true);
        $this->assertTrue($ref->getValue($drq));
    }

    public function test_view_property(): void
    {
        $ref = new \ReflectionProperty(DateRangeQuick::class, 'view');
        $ref->setAccessible(true);
        $this->assertSame('admin::filter.date-range-quick', $ref->getDefaultValue());
    }

    public function test_default_ranges_is_protected(): void
    {
        $ref = new \ReflectionMethod(DateRangeQuick::class, 'defaultRanges');
        $this->assertTrue($ref->isProtected());
    }
}
