# 提示窗

基本用法

```php
<?php

use Dcat\Admin\Widgets\Tooltip;
use Dcat\Admin\Widgets\Card;
use Dcat\Admin\Layout\Content;

class MyController
{
    public function index(Content $content)
    {
        Tooltip::make('.icon-help-circle')->title('我是提示信息');

        return $content->body(new Card(
            <<<HTML
<div class="p4">
    <i class="feather icon-help-circle"></i>
</div>
HTML
        ));
    }
}
```
