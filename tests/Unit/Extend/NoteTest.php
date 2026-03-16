<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Extend;

use Dcat\Admin\Extend\Note;
use Dcat\Admin\Tests\TestCase;
use Symfony\Component\Console\Output\BufferedOutput;

class NoteTest extends TestCase
{
    protected function createNoteUser(): object
    {
        return new class
        {
            use Note;
        };
    }

    public function test_note_adds_to_notes_array_without_output(): void
    {
        $user = $this->createNoteUser();
        $user->note('Hello');
        $user->note('World');

        $this->assertCount(2, $user->notes);
        $this->assertSame('Hello', $user->notes[0]);
        $this->assertSame('World', $user->notes[1]);
    }

    public function test_note_writes_to_output_when_set(): void
    {
        $user = $this->createNoteUser();
        $output = new BufferedOutput;
        $user->setOutPut($output);

        $user->note('Test message');

        $this->assertStringContainsString('Test message', $output->fetch());
        $this->assertEmpty($user->notes);
    }

    public function test_set_output_returns_self(): void
    {
        $user = $this->createNoteUser();
        $output = new BufferedOutput;

        $result = $user->setOutPut($output);
        $this->assertSame($user, $result);
    }

    public function test_notes_default_empty(): void
    {
        $user = $this->createNoteUser();
        $this->assertIsArray($user->notes);
        $this->assertEmpty($user->notes);
    }

    public function test_output_default_null(): void
    {
        $user = $this->createNoteUser();
        $this->assertNull($user->output);
    }
}
