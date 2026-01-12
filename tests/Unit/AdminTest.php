<?php

namespace Dcat\Admin\Tests\Unit;

use Dcat\Admin\Admin;
use Dcat\Admin\Tests\TestCase;

class AdminTest extends TestCase
{
    public function test_admin_class_exists(): void
    {
        $this->assertTrue(class_exists(Admin::class));
    }

    public function test_admin_version(): void
    {
        $this->assertIsString(Admin::VERSION);
    }
}
