<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Http\Controllers;

use Dcat\Admin\Http\Controllers\HasResourceActions;
use Dcat\Admin\Tests\TestCase;

class HasResourceActionsTest extends TestCase
{
    public function test_update_proxies_to_form_update_with_same_id(): void
    {
        $controller = new class
        {
            use HasResourceActions;

            public object $formMock;

            public function __construct()
            {
                $this->formMock = new class
                {
                    public ?int $updatedId = null;

                    public function update($id)
                    {
                        $this->updatedId = $id;

                        return 'updated-'.$id;
                    }
                };
            }

            protected function form()
            {
                return $this->formMock;
            }
        };

        $result = $controller->update(7);

        $this->assertSame('updated-7', $result);
        $this->assertSame(7, $controller->formMock->updatedId);
    }

    public function test_store_proxies_to_form_store(): void
    {
        $controller = new class
        {
            use HasResourceActions;

            public object $formMock;

            public function __construct()
            {
                $this->formMock = new class
                {
                    public bool $stored = false;

                    public function store()
                    {
                        $this->stored = true;

                        return 'stored';
                    }
                };
            }

            protected function form()
            {
                return $this->formMock;
            }
        };

        $result = $controller->store();

        $this->assertSame('stored', $result);
        $this->assertTrue($controller->formMock->stored);
    }

    public function test_destroy_proxies_to_form_destroy_with_same_id(): void
    {
        $controller = new class
        {
            use HasResourceActions;

            public object $formMock;

            public function __construct()
            {
                $this->formMock = new class
                {
                    public ?int $destroyedId = null;

                    public function destroy($id)
                    {
                        $this->destroyedId = $id;

                        return 'destroyed-'.$id;
                    }
                };
            }

            protected function form()
            {
                return $this->formMock;
            }
        };

        $result = $controller->destroy(11);

        $this->assertSame('destroyed-11', $result);
        $this->assertSame(11, $controller->formMock->destroyedId);
    }
}
