<?php

namespace Dcat\Admin\Tests\Unit\Grid\Column;

use Dcat\Admin\Grid\Column\HasDisplayers;
use Dcat\Admin\Tests\TestCase;
use Mockery;

class HasDisplayersTest extends TestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }

    public function test_trait_exists(): void
    {
        $this->assertTrue(trait_exists(HasDisplayers::class));
    }
}
