<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Http\Controllers;

use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Tests\TestCase;

class AdminControllerTest extends TestCase
{
    protected function makeController(array $overrides = []): AdminController
    {
        return new class($overrides) extends AdminController
        {
            public function __construct(private array $overrides = [])
            {
                if (array_key_exists('title', $overrides)) {
                    $this->title = $overrides['title'];
                }

                if (array_key_exists('description', $overrides)) {
                    $this->description = $overrides['description'];
                }

                if (array_key_exists('translation', $overrides)) {
                    $this->translation = $overrides['translation'];
                }
            }

            public function exposedTitle()
            {
                return $this->title();
            }

            public function exposedDescription()
            {
                return $this->description();
            }

            public function exposedTranslation()
            {
                return $this->translation();
            }
        };
    }

    public function test_title_returns_property_value_when_set(): void
    {
        $controller = $this->makeController(['title' => 'Custom Title']);

        $this->assertSame('Custom Title', $controller->exposedTitle());
    }

    public function test_title_falls_back_to_admin_trans_label_when_not_set(): void
    {
        $controller = $this->makeController();

        // When title is not set, it falls back to admin_trans_label()
        $result = $controller->exposedTitle();
        $this->assertIsString($result);
    }

    public function test_description_returns_default_empty_array(): void
    {
        $controller = $this->makeController();
        $result = $controller->exposedDescription();
        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    public function test_description_returns_custom_descriptions(): void
    {
        $controller = $this->makeController([
            'description' => [
                'index' => 'List Page',
                'show' => 'Detail Page',
                'edit' => 'Edit Page',
                'create' => 'Create Page',
            ],
        ]);

        $result = $controller->exposedDescription();
        $this->assertSame('List Page', $result['index']);
        $this->assertSame('Detail Page', $result['show']);
        $this->assertSame('Edit Page', $result['edit']);
        $this->assertSame('Create Page', $result['create']);
    }

    public function test_translation_returns_null_by_default(): void
    {
        $controller = $this->makeController();

        $this->assertNull($controller->exposedTranslation());
    }

    public function test_translation_returns_custom_path(): void
    {
        $controller = $this->makeController(['translation' => 'admin.custom']);

        $this->assertSame('admin.custom', $controller->exposedTranslation());
    }

    public function test_controller_extends_illuminate_controller(): void
    {
        $controller = new class extends AdminController {};

        $this->assertInstanceOf(\Illuminate\Routing\Controller::class, $controller);
    }

    public function test_update_delegates_to_form_update(): void
    {
        $controller = new class extends AdminController
        {
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

            public function form()
            {
                return $this->formMock;
            }
        };

        $result = $controller->update(12);

        $this->assertSame('updated-12', $result);
        $this->assertSame(12, $controller->formMock->updatedId);
    }

    public function test_store_delegates_to_form_store(): void
    {
        $controller = new class extends AdminController
        {
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

            public function form()
            {
                return $this->formMock;
            }
        };

        $result = $controller->store();

        $this->assertSame('stored', $result);
        $this->assertTrue($controller->formMock->stored);
    }

    public function test_destroy_delegates_to_form_destroy(): void
    {
        $controller = new class extends AdminController
        {
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

            public function form()
            {
                return $this->formMock;
            }
        };

        $result = $controller->destroy(18);

        $this->assertSame('destroyed-18', $result);
        $this->assertSame(18, $controller->formMock->destroyedId);
    }

    public function test_update_store_destroy_prefer_custom_handlers_when_present(): void
    {
        $controller = new class extends AdminController
        {
            public function customUpdate($id)
            {
                return 'custom-update-'.$id;
            }

            public function customStore()
            {
                return 'custom-store';
            }

            public function customDestroy($id)
            {
                return 'custom-destroy-'.$id;
            }
        };

        $this->assertSame('custom-update-3', $controller->update(3));
        $this->assertSame('custom-store', $controller->store());
        $this->assertSame('custom-destroy-5', $controller->destroy(5));
    }
}
