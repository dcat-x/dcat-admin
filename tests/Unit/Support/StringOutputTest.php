<?php

namespace Dcat\Admin\Tests\Unit\Support;

use Dcat\Admin\Support\OutputFormatter;
use Dcat\Admin\Support\StringOutput;
use Dcat\Admin\Tests\TestCase;
use Symfony\Component\Console\Output\Output;

class StringOutputTest extends TestCase
{
    public function test_is_instance_of_output(): void
    {
        $output = new StringOutput;

        $this->assertInstanceOf(Output::class, $output);
    }

    public function test_default_output_property_is_empty_string(): void
    {
        $output = new StringOutput;

        $this->assertEquals('', $output->output);
    }

    public function test_default_formatter_is_output_formatter_instance(): void
    {
        $output = new StringOutput;

        $this->assertInstanceOf(OutputFormatter::class, $output->getFormatter());
    }

    public function test_writeln_accumulates_output_with_newline(): void
    {
        $output = new StringOutput;
        $output->writeln('line one');
        $output->writeln('line two');

        $this->assertStringContainsString('line one', $output->output);
        $this->assertStringContainsString('line two', $output->output);
    }

    public function test_write_does_not_add_newline(): void
    {
        $output = new StringOutput;
        $output->write('hello');

        $this->assertEquals('hello', $output->output);
    }

    public function test_clear_resets_output_to_empty_string(): void
    {
        $output = new StringOutput;
        $output->writeln('some content');
        $output->clear();

        $this->assertEquals('', $output->output);
    }

    public function test_get_content_returns_trimmed_output(): void
    {
        $output = new StringOutput;
        $output->writeln('  trimmed  ');

        $this->assertEquals('trimmed', $output->getContent());
    }

    public function test_get_content_trims_trailing_newlines(): void
    {
        $output = new StringOutput;
        $output->writeln('line one');
        $output->writeln('line two');

        $content = $output->getContent();
        $this->assertStringNotContainsString("\n", substr($content, -1));
    }
}
