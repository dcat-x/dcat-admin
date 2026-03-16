<?php

declare(strict_types=1);

namespace Dcat\Admin\Form\Concerns;

use Dcat\Admin\Form\Field;

trait HandleCascadeFields
{
    /**
     * @return Field\CascadeGroup
     */
    public function cascadeGroup(\Closure $closure, array $dependency)
    {
        $this->pushField($group = new Field\CascadeGroup($dependency));

        $closure($this);

        $this->html($group->end())->plain();

        return $group;
    }
}
