<?php

namespace Dcat\Admin\Tests\Unit\Http\Actions\Extensions;

use Dcat\Admin\Grid\Tools\AbstractTool;
use Dcat\Admin\Http\Actions\Extensions\InstallFromLocal;
use Dcat\Admin\Support\Helper;
use Dcat\Admin\Tests\TestCase;

class InstallFromLocalTest extends TestCase
{
    public function test_action_is_tool_and_renders_install_modal_button(): void
    {
        $action = new InstallFromLocal;

        $this->assertInstanceOf(AbstractTool::class, $action);

        $html = Helper::render($action->html());

        $this->assertStringContainsString('install_from_local', $html);
        $this->assertStringContainsString('btn btn-primary', $html);
        $this->assertStringContainsString('icon-folder', $html);
    }
}
