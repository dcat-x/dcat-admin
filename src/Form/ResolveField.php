<?php

namespace Dcat\Admin\Form;

trait ResolveField
{
    protected $resolvingFieldCallbacks = [];

    /**
     * @example $form->resolvingField(function ($field, $form) {
     *     ...
     * });
     *
     * @return $this
     */
    public function resolvingField(\Closure $callback)
    {
        $this->resolvingFieldCallbacks[] = $callback;

        return $this;
    }

    public function setResolvingFieldCallbacks(array $callbacks)
    {
        $this->resolvingFieldCallbacks = $callbacks;
    }

    /**
     * @return void
     */
    protected function callResolvingFieldCallbacks(Field $field)
    {
        foreach ($this->resolvingFieldCallbacks as $callback) {
            if ($callback($field, $this) === false) {
                break;
            }
        }
    }
}
