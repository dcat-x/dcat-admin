<?php

namespace Dcat\Admin\Tests\Unit\Contracts;

use Dcat\Admin\Contracts\Repository;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Tests\TestCase;
use Mockery;

class RepositoryTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_interface_exists(): void
    {
        $this->assertTrue(interface_exists(Repository::class));
    }

    public function test_has_get_key_name_method(): void
    {
        $reflection = new \ReflectionClass(Repository::class);

        $this->assertTrue($reflection->hasMethod('getKeyName'));
    }

    public function test_has_get_created_at_column_method(): void
    {
        $reflection = new \ReflectionClass(Repository::class);

        $this->assertTrue($reflection->hasMethod('getCreatedAtColumn'));
    }

    public function test_has_get_updated_at_column_method(): void
    {
        $reflection = new \ReflectionClass(Repository::class);

        $this->assertTrue($reflection->hasMethod('getUpdatedAtColumn'));
    }

    public function test_has_is_soft_deletes_method(): void
    {
        $reflection = new \ReflectionClass(Repository::class);

        $this->assertTrue($reflection->hasMethod('isSoftDeletes'));
    }

    public function test_has_get_method(): void
    {
        $reflection = new \ReflectionClass(Repository::class);

        $this->assertTrue($reflection->hasMethod('get'));
        $this->assertCount(1, $reflection->getMethod('get')->getParameters());
    }

    public function test_has_edit_method(): void
    {
        $reflection = new \ReflectionClass(Repository::class);

        $this->assertTrue($reflection->hasMethod('edit'));
        $this->assertCount(1, $reflection->getMethod('edit')->getParameters());
    }

    public function test_has_detail_method(): void
    {
        $reflection = new \ReflectionClass(Repository::class);

        $this->assertTrue($reflection->hasMethod('detail'));
        $this->assertCount(1, $reflection->getMethod('detail')->getParameters());
    }

    public function test_has_store_method(): void
    {
        $reflection = new \ReflectionClass(Repository::class);

        $this->assertTrue($reflection->hasMethod('store'));
        $this->assertCount(1, $reflection->getMethod('store')->getParameters());
    }

    public function test_has_updating_method(): void
    {
        $reflection = new \ReflectionClass(Repository::class);

        $this->assertTrue($reflection->hasMethod('updating'));
        $this->assertCount(1, $reflection->getMethod('updating')->getParameters());
    }

    public function test_has_update_method(): void
    {
        $reflection = new \ReflectionClass(Repository::class);

        $this->assertTrue($reflection->hasMethod('update'));
        $this->assertCount(1, $reflection->getMethod('update')->getParameters());
    }

    public function test_has_delete_method(): void
    {
        $reflection = new \ReflectionClass(Repository::class);

        $this->assertTrue($reflection->hasMethod('delete'));

        $params = $reflection->getMethod('delete')->getParameters();
        $this->assertCount(2, $params);
        $this->assertSame('form', $params[0]->getName());
        $this->assertSame('deletingData', $params[1]->getName());
    }

    public function test_has_deleting_method(): void
    {
        $reflection = new \ReflectionClass(Repository::class);

        $this->assertTrue($reflection->hasMethod('deleting'));
        $this->assertCount(1, $reflection->getMethod('deleting')->getParameters());
    }

    public function test_implementation_is_instance_of_repository(): void
    {
        $repo = $this->makeRepository();

        $this->assertInstanceOf(Repository::class, $repo);
    }

    public function test_get_key_name_returns_value(): void
    {
        $repo = $this->makeRepository();

        $this->assertSame('id', $repo->getKeyName());
    }

    public function test_get_created_at_column_returns_value(): void
    {
        $repo = $this->makeRepository();

        $this->assertSame('created_at', $repo->getCreatedAtColumn());
    }

    public function test_get_updated_at_column_returns_value(): void
    {
        $repo = $this->makeRepository();

        $this->assertSame('updated_at', $repo->getUpdatedAtColumn());
    }

    public function test_is_soft_deletes_returns_bool(): void
    {
        $repo = $this->makeRepository();

        $this->assertFalse($repo->isSoftDeletes());
    }

    protected function makeRepository(): Repository
    {
        return new class implements Repository
        {
            public function getKeyName()
            {
                return 'id';
            }

            public function getCreatedAtColumn()
            {
                return 'created_at';
            }

            public function getUpdatedAtColumn()
            {
                return 'updated_at';
            }

            public function isSoftDeletes()
            {
                return false;
            }

            public function get(Grid\Model $model)
            {
                return [];
            }

            public function edit(Form $form)
            {
                return [];
            }

            public function detail(Show $show)
            {
                return [];
            }

            public function store(Form $form)
            {
                return true;
            }

            public function updating(Form $form)
            {
                return [];
            }

            public function update(Form $form)
            {
                return true;
            }

            public function delete(Form $form, array $deletingData)
            {
                return true;
            }

            public function deleting(Form $form)
            {
                return [];
            }
        };
    }
}
