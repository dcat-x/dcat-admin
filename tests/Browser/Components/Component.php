<?php

namespace Tests\Browser\Components;

use Laravel\Dusk\Browser;
use Laravel\Dusk\Component as BaseComponent;

abstract class Component extends BaseComponent
{
    /**
     * 解析css选择器别名.
     *
     * @param  string  $selector
     * @return string
     */
    public function formatSelector(Browser $browser, $selector = '')
    {
        return $browser->resolver->format($selector);
    }

    /**
     * 解析css选择器别名但不使用前缀.
     *
     * @param  string  $selector
     * @return string
     */
    public function formatSelectorWithoutPrefix(Browser $browser, $selector = '')
    {
        $resolver = clone $browser->resolver;

        $resolver->prefix = '';

        return $resolver->format($selector);
    }

    /**
     * @return string
     */
    public function parentSelector(Browser $browser)
    {
        return $browser->resolver->prefix;
    }
}
