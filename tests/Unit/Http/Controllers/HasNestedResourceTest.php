<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Http\Controllers;

use Dcat\Admin\Http\Controllers\HasNestedResource;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Tests\TestCase;
use Illuminate\Http\Request;

class HasNestedResourceTest extends TestCase
{
    public function test_show_and_edit_proxy_to_parent_with_nested_resource_id(): void
    {
        $controller = new class extends FakeNestedParentController
        {
            use HasNestedResource;
        };

        $controller->setNestedResourceId(123);
        $content = $this->mockContent();

        $showResult = $controller->show(999, $content);
        $editResult = $controller->edit(888, $content);

        $this->assertSame('show-123', $showResult);
        $this->assertSame('edit-123', $editResult);
        $this->assertSame(123, $controller->parentShowId);
        $this->assertSame(123, $controller->parentEditId);
    }

    public function test_update_and_destroy_proxy_to_parent_with_nested_resource_id(): void
    {
        $controller = new class extends FakeNestedParentController
        {
            use HasNestedResource;
        };

        $controller->setNestedResourceId(55);

        $updateResult = $controller->update(1);
        $destroyResult = $controller->destroy(2);

        $this->assertSame('update-55', $updateResult);
        $this->assertSame('destroy-55', $destroyResult);
        $this->assertSame(55, $controller->parentUpdateId);
        $this->assertSame(55, $controller->parentDestroyId);
    }

    public function test_get_nested_resource_id_reads_request_parameter_with_route_name(): void
    {
        $request = Request::create('/nested', 'GET', ['child_id' => '789']);
        $request->setRouteResolver(function () {
            return new class
            {
                public function parameterNames(): array
                {
                    return ['parent_id', 'child_id'];
                }
            };
        });
        $this->app->instance('request', $request);

        $controller = new class extends FakeNestedParentController
        {
            use HasNestedResource;
        };

        $this->assertSame('789', $controller->getNestedResourceId());
    }

    public function test_get_route_parameter_name_can_be_set_explicitly(): void
    {
        $controller = new class extends FakeNestedParentController
        {
            use HasNestedResource;
        };

        $controller->setRouteParameterName('member_id');

        $this->assertSame('member_id', $controller->getRouteParameterName());
    }

    protected function mockContent(): Content
    {
        return $this->createMock(Content::class);
    }
}

class FakeNestedParentController
{
    public $parentShowId;

    public $parentEditId;

    public $parentUpdateId;

    public $parentDestroyId;

    public function show($id, Content $content)
    {
        $this->parentShowId = $id;

        return 'show-'.$id;
    }

    public function edit($id, Content $content)
    {
        $this->parentEditId = $id;

        return 'edit-'.$id;
    }

    public function update($id)
    {
        $this->parentUpdateId = $id;

        return 'update-'.$id;
    }

    public function destroy($id)
    {
        $this->parentDestroyId = $id;

        return 'destroy-'.$id;
    }
}
