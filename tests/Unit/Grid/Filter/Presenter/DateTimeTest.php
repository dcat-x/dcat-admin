<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Grid\Filter\Presenter;

use Dcat\Admin\Grid\Filter\AbstractFilter;
use Dcat\Admin\Grid\Filter\Presenter\DateTime;
use Dcat\Admin\Tests\TestCase;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use ReflectionProperty;

#[AllowMockObjectsWithoutExpectations]
class DateTimeTest extends TestCase
{
    protected function makeDateTime(array $options = []): DateTime
    {
        return new DateTime($options);
    }

    protected function attachFilter(DateTime $dt): void
    {
        $filter = $this->createMock(AbstractFilter::class);
        $filter->method('column')->willReturn('created_at');
        $filter->group = null;

        $dt->setParent($filter);
    }

    public function test_constructor_sets_default_format(): void
    {
        $dt = $this->makeDateTime();

        $ref = new ReflectionProperty($dt, 'options');
        $ref->setAccessible(true);
        $options = $ref->getValue($dt);

        $this->assertSame('YYYY-MM-DD HH:mm:ss', $options['format']);
    }

    public function test_constructor_with_custom_format(): void
    {
        $dt = $this->makeDateTime(['format' => 'YYYY-MM-DD']);

        $ref = new ReflectionProperty($dt, 'options');
        $ref->setAccessible(true);
        $options = $ref->getValue($dt);

        $this->assertSame('YYYY-MM-DD', $options['format']);
    }

    public function test_constructor_sets_locale_from_config(): void
    {
        $dt = $this->makeDateTime();

        $ref = new ReflectionProperty($dt, 'options');
        $ref->setAccessible(true);
        $options = $ref->getValue($dt);

        $this->assertSame(config('app.locale'), $options['locale'] ?? null);
    }

    public function test_constructor_with_custom_locale(): void
    {
        $dt = $this->makeDateTime(['locale' => 'zh-CN']);

        $ref = new ReflectionProperty($dt, 'options');
        $ref->setAccessible(true);
        $options = $ref->getValue($dt);

        $this->assertSame('zh-CN', $options['locale']);
    }

    public function test_constructor_preserves_extra_options(): void
    {
        $dt = $this->makeDateTime(['minDate' => '2020-01-01', 'maxDate' => '2025-12-31']);

        $ref = new ReflectionProperty($dt, 'options');
        $ref->setAccessible(true);
        $options = $ref->getValue($dt);

        $this->assertSame('2020-01-01', $options['minDate']);
        $this->assertSame('2025-12-31', $options['maxDate']);
        $this->assertIsString($options['format'] ?? null);
    }

    public function test_default_variables_contains_options_and_group(): void
    {
        $dt = $this->makeDateTime();
        $this->attachFilter($dt);

        $vars = $dt->defaultVariables();

        $this->assertArrayContainsKeys(['options', 'group'], $vars);
        $this->assertIsArray($vars['options'] ?? null);
    }

    public function test_view_returns_datetime_view(): void
    {
        $dt = $this->makeDateTime();

        $this->assertSame('admin::filter.datetime', $dt->view());
    }

    private function assertArrayContainsKeys(array $expectedKeys, array $actual): void
    {
        $keys = array_keys($actual);

        foreach ($expectedKeys as $key) {
            $this->assertContains($key, $keys);
        }
    }
}
