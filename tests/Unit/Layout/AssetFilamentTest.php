<?php

namespace Dcat\Admin\Tests\Unit\Layout;

use Dcat\Admin\Layout\Asset;
use Dcat\Admin\Tests\TestCase;

class AssetFilamentTest extends TestCase
{
    public function test_filament_aliases_exist(): void
    {
        $asset = new Asset();

        $filamentForms = $asset->getAlias('@filament-forms');
        $this->assertNotEmpty($filamentForms['css']);
        $this->assertNotEmpty($filamentForms['js']);
    }
}
