<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Http\Repositories;

use Dcat\Admin\Http\Repositories\Extension;
use Dcat\Admin\Repositories\Repository;
use Dcat\Admin\Tests\TestCase;
use Mockery;

class ExtensionTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_extension_is_repository_instance(): void
    {
        $repository = new Extension;

        $this->assertInstanceOf(Repository::class, $repository);
    }

    public function test_edit_detail_updating_and_deleting_return_empty_array(): void
    {
        $repository = new Extension;
        $form = Mockery::mock(\Dcat\Admin\Form::class);
        $show = new \Dcat\Admin\Show([]);

        $this->assertSame([], $repository->edit($form));
        $this->assertSame([], $repository->updating($form));
        $this->assertSame([], $repository->detail($show));
        $this->assertSame([], $repository->deleting($form));
    }

    public function test_each_method_is_protected(): void
    {
        $reflection = new \ReflectionMethod(Extension::class, 'each');
        $this->assertTrue($reflection->isProtected());
    }

    public function test_update_and_move_order_methods_return_true(): void
    {
        $repository = new Extension;
        $form = Mockery::mock(\Dcat\Admin\Form::class);

        $this->assertTrue($repository->update($form));
        $this->assertTrue($repository->moveOrderUp());
        $this->assertTrue($repository->moveOrderDown());
    }
}
