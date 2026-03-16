<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Support;

use Dcat\Admin\Support\OutputFormatter;
use Dcat\Admin\Tests\TestCase;
use Symfony\Component\Console\Formatter\OutputFormatter as SymfonyOutputFormatter;

class OutputFormatterTest extends TestCase
{
    public function test_is_instance_of_symfony_output_formatter(): void
    {
        $formatter = new OutputFormatter;

        $this->assertInstanceOf(SymfonyOutputFormatter::class, $formatter);
    }

    public function test_format_returns_message_unchanged(): void
    {
        $formatter = new OutputFormatter;

        $this->assertSame('hello world', $formatter->format('hello world'));
    }

    public function test_format_returns_null_when_null_given(): void
    {
        $formatter = new OutputFormatter;

        $this->assertNull($formatter->format(null));
    }

    public function test_format_returns_empty_string_when_empty_given(): void
    {
        $formatter = new OutputFormatter;

        $this->assertSame('', $formatter->format(''));
    }

    public function test_format_returns_string_with_special_characters(): void
    {
        $formatter = new OutputFormatter;
        $message = '<info>some info</info>';

        $this->assertSame($message, $formatter->format($message));
    }
}
