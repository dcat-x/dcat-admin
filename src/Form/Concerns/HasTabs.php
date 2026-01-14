<?php

namespace Dcat\Admin\Form\Concerns;

use Closure;
use Dcat\Admin\Form\Tab;

trait HasTabs
{
    /**
     * @var Tab
     */
    protected $tab = null;

    /**
     * Use tab to split form.
     *
     * @param  string  $title
     * @param  bool  $active
     * @return $this
     */
    public function tab($title, Closure $content, $active = false, ?string $id = null)
    {
        $this->getTab()->append($title, $content, $active, $id);

        return $this;
    }

    public function hasTab()
    {
        return (bool) $this->tab;
    }

    /**
     * Get Tab instance.
     *
     * @return Tab
     */
    public function getTab()
    {
        if (is_null($this->tab)) {
            $this->tab = new Tab($this);
        }

        return $this->tab;
    }
}
