<?php

declare(strict_types=1);

namespace Dcat\Admin\Grid\Displayers;

class Link extends AbstractDisplayer
{
    public function display($href = '', $target = '_blank')
    {
        if ($href instanceof \Closure) {
            $href = $href->bindTo($this->row);

            $href = $href($this->value);
        } else {
            $href = $href ?: $this->value;
        }

        return "<a href='$href' target='$target'>{$this->value}</a>";
    }
}
