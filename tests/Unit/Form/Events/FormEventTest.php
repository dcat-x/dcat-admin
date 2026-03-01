<?php

namespace Dcat\Admin\Tests\Unit\Form\Events;

use Dcat\Admin\Form;
use Dcat\Admin\Form\Events\Creating;
use Dcat\Admin\Form\Events\Deleted;
use Dcat\Admin\Form\Events\Deleting;
use Dcat\Admin\Form\Events\Editing;
use Dcat\Admin\Form\Events\Event;
use Dcat\Admin\Form\Events\FileDeleted;
use Dcat\Admin\Form\Events\FileDeleting;
use Dcat\Admin\Form\Events\Saved;
use Dcat\Admin\Form\Events\Saving;
use Dcat\Admin\Form\Events\Submitted;
use Dcat\Admin\Form\Events\Uploaded;
use Dcat\Admin\Form\Events\Uploading;
use Dcat\Admin\Tests\TestCase;
use Mockery;

class FormEventTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    protected function createMockForm(): Form
    {
        return Mockery::mock(Form::class);
    }

    public function test_creating_event_can_be_instantiated(): void
    {
        $form = $this->createMockForm();
        $event = new Creating($form);

        $this->assertInstanceOf(Creating::class, $event);
        $this->assertInstanceOf(Event::class, $event);
    }

    public function test_deleted_event_can_be_instantiated(): void
    {
        $form = $this->createMockForm();
        $event = new Deleted($form);

        $this->assertInstanceOf(Deleted::class, $event);
        $this->assertInstanceOf(Event::class, $event);
    }

    public function test_deleting_event_can_be_instantiated(): void
    {
        $form = $this->createMockForm();
        $event = new Deleting($form);

        $this->assertInstanceOf(Deleting::class, $event);
        $this->assertInstanceOf(Event::class, $event);
    }

    public function test_editing_event_can_be_instantiated(): void
    {
        $form = $this->createMockForm();
        $event = new Editing($form);

        $this->assertInstanceOf(Editing::class, $event);
        $this->assertInstanceOf(Event::class, $event);
    }

    public function test_file_deleted_event_can_be_instantiated(): void
    {
        $form = $this->createMockForm();
        $event = new FileDeleted($form);

        $this->assertInstanceOf(FileDeleted::class, $event);
        $this->assertInstanceOf(Event::class, $event);
    }

    public function test_file_deleting_event_can_be_instantiated(): void
    {
        $form = $this->createMockForm();
        $event = new FileDeleting($form);

        $this->assertInstanceOf(FileDeleting::class, $event);
        $this->assertInstanceOf(Event::class, $event);
    }

    public function test_saved_event_can_be_instantiated(): void
    {
        $form = $this->createMockForm();
        $event = new Saved($form);

        $this->assertInstanceOf(Saved::class, $event);
        $this->assertInstanceOf(Event::class, $event);
    }

    public function test_saving_event_can_be_instantiated(): void
    {
        $form = $this->createMockForm();
        $event = new Saving($form);

        $this->assertInstanceOf(Saving::class, $event);
        $this->assertInstanceOf(Event::class, $event);
    }

    public function test_submitted_event_can_be_instantiated(): void
    {
        $form = $this->createMockForm();
        $event = new Submitted($form);

        $this->assertInstanceOf(Submitted::class, $event);
        $this->assertInstanceOf(Event::class, $event);
    }

    public function test_uploaded_event_can_be_instantiated(): void
    {
        $form = $this->createMockForm();
        $event = new Uploaded($form);

        $this->assertInstanceOf(Uploaded::class, $event);
        $this->assertInstanceOf(Event::class, $event);
    }

    public function test_uploading_event_can_be_instantiated(): void
    {
        $form = $this->createMockForm();
        $event = new Uploading($form);

        $this->assertInstanceOf(Uploading::class, $event);
        $this->assertInstanceOf(Event::class, $event);
    }

    public function test_constructor_sets_form_property(): void
    {
        $form = $this->createMockForm();
        $event = new Creating($form);

        $this->assertSame($form, $event->form);
    }

    public function test_default_payload_is_empty_array(): void
    {
        $form = $this->createMockForm();
        $event = new Creating($form);

        $this->assertIsArray($event->payload);
        $this->assertEmpty($event->payload);
    }

    public function test_constructor_sets_custom_payload(): void
    {
        $form = $this->createMockForm();
        $payload = ['key' => 'value', 'id' => 42];
        $event = new Saving($form, $payload);

        $this->assertSame($payload, $event->payload);
    }

    public function test_each_event_stores_form_correctly(): void
    {
        $form = $this->createMockForm();
        $eventClasses = [
            Creating::class,
            Deleted::class,
            Deleting::class,
            Editing::class,
            FileDeleted::class,
            FileDeleting::class,
            Saved::class,
            Saving::class,
            Submitted::class,
            Uploaded::class,
            Uploading::class,
        ];

        foreach ($eventClasses as $eventClass) {
            $event = new $eventClass($form);
            $this->assertSame($form, $event->form, "Failed asserting form is set for {$eventClass}");
        }
    }

    public function test_each_event_has_default_empty_payload(): void
    {
        $form = $this->createMockForm();
        $eventClasses = [
            Creating::class,
            Deleted::class,
            Deleting::class,
            Editing::class,
            FileDeleted::class,
            FileDeleting::class,
            Saved::class,
            Saving::class,
            Submitted::class,
            Uploaded::class,
            Uploading::class,
        ];

        foreach ($eventClasses as $eventClass) {
            $event = new $eventClass($form);
            $this->assertIsArray($event->payload, "Failed asserting payload is array for {$eventClass}");
            $this->assertEmpty($event->payload, "Failed asserting payload is empty for {$eventClass}");
        }
    }

    public function test_each_event_accepts_custom_payload(): void
    {
        $form = $this->createMockForm();
        $payload = ['action' => 'test', 'data' => [1, 2, 3]];
        $eventClasses = [
            Creating::class,
            Deleted::class,
            Deleting::class,
            Editing::class,
            FileDeleted::class,
            FileDeleting::class,
            Saved::class,
            Saving::class,
            Submitted::class,
            Uploaded::class,
            Uploading::class,
        ];

        foreach ($eventClasses as $eventClass) {
            $event = new $eventClass($form, $payload);
            $this->assertSame($payload, $event->payload, "Failed asserting payload is set for {$eventClass}");
        }
    }
}
