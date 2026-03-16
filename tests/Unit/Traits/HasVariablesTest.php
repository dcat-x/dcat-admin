<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Traits;

use Dcat\Admin\Tests\TestCase;
use Dcat\Admin\Traits\HasVariables;

class HasVariablesTest extends TestCase
{
    public function test_variables_returns_empty_array_by_default(): void
    {
        $obj = new class
        {
            use HasVariables;
        };

        $this->assertSame([], $obj->variables());
    }

    public function test_add_variables(): void
    {
        $obj = new class
        {
            use HasVariables;
        };

        $result = $obj->addVariables(['key' => 'value']);

        $this->assertSame($obj, $result);
        $this->assertSame(['key' => 'value'], $obj->variables());
    }

    public function test_add_variables_merges(): void
    {
        $obj = new class
        {
            use HasVariables;
        };

        $obj->addVariables(['a' => 1]);
        $obj->addVariables(['b' => 2]);

        $this->assertSame(['a' => 1, 'b' => 2], $obj->variables());
    }

    public function test_add_variables_overwrites_existing_keys(): void
    {
        $obj = new class
        {
            use HasVariables;
        };

        $obj->addVariables(['key' => 'original']);
        $obj->addVariables(['key' => 'updated']);

        $this->assertSame(['key' => 'updated'], $obj->variables());
    }

    public function test_variables_merges_with_default_variables(): void
    {
        $obj = new class
        {
            use HasVariables;

            public function defaultVariables(): array
            {
                return ['default_key' => 'default_value'];
            }
        };

        $obj->addVariables(['custom_key' => 'custom_value']);

        $vars = $obj->variables();

        $this->assertSame('default_value', $vars['default_key']);
        $this->assertSame('custom_value', $vars['custom_key']);
    }

    public function test_custom_variables_override_defaults(): void
    {
        $obj = new class
        {
            use HasVariables;

            public function defaultVariables(): array
            {
                return ['key' => 'default'];
            }
        };

        $obj->addVariables(['key' => 'custom']);

        $this->assertSame('custom', $obj->variables()['key']);
    }

    public function test_add_empty_variables(): void
    {
        $obj = new class
        {
            use HasVariables;
        };

        $obj->addVariables(['existing' => 'val']);
        $obj->addVariables([]);

        $this->assertSame(['existing' => 'val'], $obj->variables());
    }
}
