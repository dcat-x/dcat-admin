<?php

namespace Dcat\Admin\Grid\Filter\Presenter;

class Toggle extends Presenter
{
    /**
     * @var string
     */
    protected $view = 'admin::filter.toggle';

    /**
     * @var string
     */
    protected $onText;

    /**
     * @var string
     */
    protected $offText;

    /**
     * @var mixed
     */
    protected $onValue = 1;

    /**
     * @var mixed
     */
    protected $offValue = 0;

    /**
     * @var string
     */
    protected $size = 'small';

    /**
     * Toggle constructor.
     *
     * @param  string|null  $onText
     * @param  string|null  $offText
     */
    public function __construct($onText = null, $offText = null)
    {
        $this->onText = $onText ?? trans('admin.yes');
        $this->offText = $offText ?? trans('admin.no');
    }

    /**
     * Set on/off values.
     *
     * @param  mixed  $on
     * @param  mixed  $off
     * @return $this
     */
    public function values($on, $off = 0)
    {
        $this->onValue = $on;
        $this->offValue = $off;

        return $this;
    }

    /**
     * Set text labels.
     *
     * @param  string  $on
     * @param  string  $off
     * @return $this
     */
    public function text($on, $off)
    {
        $this->onText = $on;
        $this->offText = $off;

        return $this;
    }

    /**
     * Set toggle size.
     *
     * @param  string  $size
     * @return $this
     */
    public function size($size)
    {
        $this->size = $size;

        return $this;
    }

    public function defaultVariables(): array
    {
        return [
            'onText' => $this->onText,
            'offText' => $this->offText,
            'onValue' => $this->onValue,
            'offValue' => $this->offValue,
            'size' => $this->size,
        ];
    }
}
