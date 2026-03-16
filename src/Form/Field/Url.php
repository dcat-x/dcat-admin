<?php

declare(strict_types=1);

namespace Dcat\Admin\Form\Field;

class Url extends Text
{
    protected $rules = ['nullable', 'url'];

    public function render()
    {
        $this->prepend('<i class="fa fa-internet-explorer fa-fw"></i>')
            ->defaultAttribute('type', 'url');

        return parent::render();
    }
}
