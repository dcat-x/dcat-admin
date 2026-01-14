<?php

namespace Dcat\Admin\Form\Field;

use Dcat\Admin\Admin;

/**
 * 货币金额输入字段
 *
 * 用于处理以"分"为单位存储的金额，自动在输入时转换为"元"，
 * 提交时转换回"分"。
 */
class Fee extends Currency
{
    /**
     * 设置 inputmask 选项，处理分/元转换.
     */
    public function inputmask($options): static
    {
        Admin::js('@jquery.inputmask');

        // 显示时将分转换为元（除以100）
        $options['onBeforeMask'] = <<<'EOT'
function(value, opts) {
    return (value / 100).toPrecision(12);
}
EOT;

        // 粘贴时保持原值
        $options['onBeforePaste'] = <<<'EOT'
function(value, opts) {
    return value;
}
EOT;

        $options['autoUnmask'] = true;

        // 提交时将元转换回分（乘以100）
        $options['onUnMask'] = <<<'EOT'
function(maskedValue, unmaskedValue, opts) {
    return (unmaskedValue * 100).toPrecision(12);
}
EOT;

        // 禁用千分位分隔符，避免数值转换问题
        $options['groupSeparator'] = '';

        $options = json_encode($options, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);

        $this->script = "Dcat.init('{$this->getElementClassSelector()}', function (self) {
            self.inputmask({$options}).on('focus',function(){
                var that = $(this);
                setTimeout(function(){
                  that.select();
                },1)
            });
        });";

        return $this;
    }

    /**
     * 渲染组件.
     */
    public function render(): mixed
    {
        $this->symbol('$')->defaultAttribute(
            'value',
            old($this->elementName ?: $this->column, $this->value())
        );

        return parent::render();
    }
}
