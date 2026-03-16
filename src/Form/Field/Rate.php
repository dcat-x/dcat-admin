<?php

declare(strict_types=1);

namespace Dcat\Admin\Form\Field;

class Rate extends Text
{
    public function render()
    {
        $this->prepend('%')->defaultAttribute('placeholder', 0);

        return parent::render();
    }
}
