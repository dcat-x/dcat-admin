<?php

namespace Dcat\Admin\Tests\Unit\Form\Concerns;

use Dcat\Admin\Form\Concerns\HasEvents;
use Dcat\Admin\Tests\TestCase;
use Illuminate\Support\Facades\Event;
use Mockery;

class FormHasEventsTestHelper
{
    use HasEvents;

    protected function fire($name, array $payload = [])
    {
        Event::dispatch(new $name($this, $payload));

        return $this->eventResponse;
    }

    public function model()
    {
        return null;
    }
}

class HasEventsTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_creating_returns_self(): void
    {
        $helper = new FormHasEventsTestHelper;

        $result = $helper->creating(function () {});

        $this->assertSame($helper, $result);
    }

    public function test_editing_returns_self(): void
    {
        $helper = new FormHasEventsTestHelper;

        $result = $helper->editing(function () {});

        $this->assertSame($helper, $result);
    }

    public function test_submitted_returns_self(): void
    {
        $helper = new FormHasEventsTestHelper;

        $result = $helper->submitted(function () {});

        $this->assertSame($helper, $result);
    }

    public function test_saving_returns_self(): void
    {
        $helper = new FormHasEventsTestHelper;

        $result = $helper->saving(function () {});

        $this->assertSame($helper, $result);
    }

    public function test_saved_returns_self(): void
    {
        $helper = new FormHasEventsTestHelper;

        $result = $helper->saved(function () {});

        $this->assertSame($helper, $result);
    }

    public function test_deleting_returns_self(): void
    {
        $helper = new FormHasEventsTestHelper;

        $result = $helper->deleting(function () {});

        $this->assertSame($helper, $result);
    }

    public function test_deleted_returns_self(): void
    {
        $helper = new FormHasEventsTestHelper;

        $result = $helper->deleted(function () {});

        $this->assertSame($helper, $result);
    }

    public function test_uploading_returns_self(): void
    {
        $helper = new FormHasEventsTestHelper;

        $result = $helper->uploading(function () {});

        $this->assertSame($helper, $result);
    }

    public function test_uploaded_returns_self(): void
    {
        $helper = new FormHasEventsTestHelper;

        $result = $helper->uploaded(function () {});

        $this->assertSame($helper, $result);
    }

    public function test_file_deleting_returns_self(): void
    {
        $helper = new FormHasEventsTestHelper;

        $result = $helper->fileDeleting(function () {});

        $this->assertSame($helper, $result);
    }

    public function test_file_deleted_returns_self(): void
    {
        $helper = new FormHasEventsTestHelper;

        $result = $helper->fileDeleted(function () {});

        $this->assertSame($helper, $result);
    }

    public function test_event_response_initially_null(): void
    {
        $helper = new FormHasEventsTestHelper;

        $this->assertNull($helper->eventResponse);
    }

    public function test_all_event_methods_accept_closure(): void
    {
        $helper = new FormHasEventsTestHelper;

        $methods = [
            'creating', 'editing', 'submitted', 'saving', 'saved',
            'deleting', 'deleted', 'uploading', 'uploaded',
            'fileDeleting', 'fileDeleted',
        ];

        foreach ($methods as $method) {
            $ref = new \ReflectionMethod($helper, $method);
            $params = $ref->getParameters();
            $this->assertCount(1, $params, "Method {$method} should accept exactly one parameter");
            $this->assertSame('Closure', $params[0]->getType()->getName(), "Method {$method} should accept a Closure");
        }
    }
}
