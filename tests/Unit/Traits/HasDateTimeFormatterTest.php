<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Traits;

use Dcat\Admin\Tests\TestCase;
use Dcat\Admin\Traits\HasDateTimeFormatter;

class HasDateTimeFormatterTestModel
{
    use HasDateTimeFormatter;

    protected string $dateFormat = 'Y-m-d H:i:s';

    public function getDateFormat(): string
    {
        return $this->dateFormat;
    }

    public function setDateFormat(string $format): void
    {
        $this->dateFormat = $format;
    }

    /**
     * Expose protected serializeDate for testing.
     */
    public function callSerializeDate(\DateTimeInterface $date): string
    {
        return $this->serializeDate($date);
    }
}

class HasDateTimeFormatterTest extends TestCase
{
    public function test_serialize_date_with_default_format(): void
    {
        $model = new HasDateTimeFormatterTestModel;

        $date = new \DateTime('2024-06-15 14:30:00');
        $result = $model->callSerializeDate($date);

        $this->assertSame('2024-06-15 14:30:00', $result);
    }

    public function test_serialize_date_with_custom_format(): void
    {
        $model = new HasDateTimeFormatterTestModel;
        $model->setDateFormat('Y-m-d');

        $date = new \DateTime('2024-06-15 14:30:00');
        $result = $model->callSerializeDate($date);

        $this->assertSame('2024-06-15', $result);
    }

    public function test_serialize_date_with_time_only_format(): void
    {
        $model = new HasDateTimeFormatterTestModel;
        $model->setDateFormat('H:i:s');

        $date = new \DateTime('2024-06-15 14:30:45');
        $result = $model->callSerializeDate($date);

        $this->assertSame('14:30:45', $result);
    }

    public function test_serialize_date_with_date_time_immutable(): void
    {
        $model = new HasDateTimeFormatterTestModel;

        $date = new \DateTimeImmutable('2024-01-01 00:00:00');
        $result = $model->callSerializeDate($date);

        $this->assertSame('2024-01-01 00:00:00', $result);
    }

    public function test_serialize_date_with_unix_timestamp_format(): void
    {
        $model = new HasDateTimeFormatterTestModel;
        $model->setDateFormat('U');

        $date = new \DateTime('2024-01-01 00:00:00', new \DateTimeZone('UTC'));
        $result = $model->callSerializeDate($date);

        $this->assertSame('1704067200', $result);
    }

    public function test_serialize_date_with_iso_format(): void
    {
        $model = new HasDateTimeFormatterTestModel;
        $model->setDateFormat('c');

        $date = new \DateTime('2024-06-15 14:30:00', new \DateTimeZone('UTC'));
        $result = $model->callSerializeDate($date);

        $this->assertStringContainsString('2024-06-15T14:30:00', $result);
    }

    public function test_serialize_date_with_custom_separator_format(): void
    {
        $model = new HasDateTimeFormatterTestModel;
        $model->setDateFormat('d/m/Y');

        $date = new \DateTime('2024-06-15');
        $result = $model->callSerializeDate($date);

        $this->assertSame('15/06/2024', $result);
    }
}
