<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Form\Field;

use Dcat\Admin\Form\Field;
use Dcat\Admin\Form\Field\CascadeGroup;
use Dcat\Admin\Tests\TestCase;
use Mockery;

class CascadeGroupTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_constructor_sets_dependency(): void
    {
        $dependency = ['column' => 'type', 'class' => 'type-1', 'index' => 0];
        $group = new CascadeGroup($dependency);

        $this->assertInstanceOf(CascadeGroup::class, $group);
        $this->assertInstanceOf(Field::class, $group);
    }

    public function test_depends_on_returns_true_for_matching_field(): void
    {
        $dependency = ['column' => 'type', 'class' => 'type-1', 'index' => 0];
        $group = new CascadeGroup($dependency);

        $field = Mockery::mock(Field::class);
        $field->shouldReceive('column')->andReturn('type');

        $this->assertTrue($group->dependsOn($field));
    }

    public function test_depends_on_returns_false_for_non_matching_field(): void
    {
        $dependency = ['column' => 'type', 'class' => 'type-1', 'index' => 0];
        $group = new CascadeGroup($dependency);

        $field = Mockery::mock(Field::class);
        $field->shouldReceive('column')->andReturn('status');

        $this->assertFalse($group->dependsOn($field));
    }

    public function test_index_returns_dependency_index(): void
    {
        $dependency = ['column' => 'type', 'class' => 'type-1', 'index' => 3];
        $group = new CascadeGroup($dependency);

        $this->assertSame(3, $group->index());
    }

    public function test_visiable_removes_hide_class(): void
    {
        $dependency = ['column' => 'type', 'class' => 'type-1', 'index' => 0];
        $group = new CascadeGroup($dependency);

        $group->visiable();

        $rendered = $group->render();
        $this->assertStringNotContainsString('d-none', $rendered);
    }

    public function test_render_contains_cascade_group_class(): void
    {
        $dependency = ['column' => 'type', 'class' => 'type-1', 'index' => 0];
        $group = new CascadeGroup($dependency);

        $rendered = $group->render();
        $this->assertStringContainsString('cascade-group', $rendered);
        $this->assertStringContainsString('type-1', $rendered);
        $this->assertStringContainsString('d-none', $rendered);
    }

    public function test_end_returns_closing_div(): void
    {
        $dependency = ['column' => 'type', 'class' => 'type-1', 'index' => 0];
        $group = new CascadeGroup($dependency);

        $this->assertSame('</div>', $group->end());
    }
}
