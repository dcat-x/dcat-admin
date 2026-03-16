<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Support;

use Dcat\Admin\Tests\TestCase;
use Mockery;

class HelpersTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    // -------------------------------------------------------
    // Function existence
    // -------------------------------------------------------

    public function test_admin_setting_exists(): void
    {
        $this->assertTrue(function_exists('admin_setting'));
    }

    public function test_admin_setting_array_exists(): void
    {
        $this->assertTrue(function_exists('admin_setting_array'));
    }

    public function test_admin_extension_setting_exists(): void
    {
        $this->assertTrue(function_exists('admin_extension_setting'));
    }

    public function test_admin_section_exists(): void
    {
        $this->assertTrue(function_exists('admin_section'));
    }

    public function test_admin_has_section_exists(): void
    {
        $this->assertTrue(function_exists('admin_has_section'));
    }

    public function test_admin_inject_section_exists(): void
    {
        $this->assertTrue(function_exists('admin_inject_section'));
    }

    public function test_admin_inject_section_if_exists(): void
    {
        $this->assertTrue(function_exists('admin_inject_section_if'));
    }

    public function test_admin_has_default_section_exists(): void
    {
        $this->assertTrue(function_exists('admin_has_default_section'));
    }

    public function test_admin_inject_default_section_exists(): void
    {
        $this->assertTrue(function_exists('admin_inject_default_section'));
    }

    public function test_admin_trans_field_exists(): void
    {
        $this->assertTrue(function_exists('admin_trans_field'));
    }

    public function test_admin_trans_label_exists(): void
    {
        $this->assertTrue(function_exists('admin_trans_label'));
    }

    public function test_admin_trans_option_exists(): void
    {
        $this->assertTrue(function_exists('admin_trans_option'));
    }

    public function test_admin_trans_exists(): void
    {
        $this->assertTrue(function_exists('admin_trans'));
    }

    public function test_admin_controller_slug_exists(): void
    {
        $this->assertTrue(function_exists('admin_controller_slug'));
    }

    public function test_admin_controller_name_exists(): void
    {
        $this->assertTrue(function_exists('admin_controller_name'));
    }

    public function test_admin_path_exists(): void
    {
        $this->assertTrue(function_exists('admin_path'));
    }

    public function test_admin_url_exists(): void
    {
        $this->assertTrue(function_exists('admin_url'));
    }

    public function test_admin_base_path_exists(): void
    {
        $this->assertTrue(function_exists('admin_base_path'));
    }

    public function test_admin_toastr_exists(): void
    {
        $this->assertTrue(function_exists('admin_toastr'));
    }

    public function test_admin_success_exists(): void
    {
        $this->assertTrue(function_exists('admin_success'));
    }

    public function test_admin_error_exists(): void
    {
        $this->assertTrue(function_exists('admin_error'));
    }

    public function test_admin_warning_exists(): void
    {
        $this->assertTrue(function_exists('admin_warning'));
    }

    public function test_admin_info_exists(): void
    {
        $this->assertTrue(function_exists('admin_info'));
    }

    public function test_admin_asset_exists(): void
    {
        $this->assertTrue(function_exists('admin_asset'));
    }

    public function test_admin_route_exists(): void
    {
        $this->assertTrue(function_exists('admin_route'));
    }

    public function test_admin_route_name_exists(): void
    {
        $this->assertTrue(function_exists('admin_route_name'));
    }

    public function test_admin_api_route_name_exists(): void
    {
        $this->assertTrue(function_exists('admin_api_route_name'));
    }

    public function test_admin_extension_path_exists(): void
    {
        $this->assertTrue(function_exists('admin_extension_path'));
    }

    public function test_admin_color_exists(): void
    {
        $this->assertTrue(function_exists('admin_color'));
    }

    public function test_admin_view_exists(): void
    {
        $this->assertTrue(function_exists('admin_view'));
    }

    public function test_admin_script_exists(): void
    {
        $this->assertTrue(function_exists('admin_script'));
    }

    public function test_admin_style_exists(): void
    {
        $this->assertTrue(function_exists('admin_style'));
    }

    public function test_admin_js_exists(): void
    {
        $this->assertTrue(function_exists('admin_js'));
    }

    public function test_admin_css_exists(): void
    {
        $this->assertTrue(function_exists('admin_css'));
    }

    public function test_admin_require_assets_exists(): void
    {
        $this->assertTrue(function_exists('admin_require_assets'));
    }

    public function test_admin_javascript_exists(): void
    {
        $this->assertTrue(function_exists('admin_javascript'));
    }

    public function test_admin_javascript_json_exists(): void
    {
        $this->assertTrue(function_exists('admin_javascript_json'));
    }

    public function test_admin_exit_exists(): void
    {
        $this->assertTrue(function_exists('admin_exit'));
    }

    public function test_admin_redirect_exists(): void
    {
        $this->assertTrue(function_exists('admin_redirect'));
    }

    public function test_format_byte_exists(): void
    {
        $this->assertTrue(function_exists('format_byte'));
    }

    public function test_money_formatter_exists(): void
    {
        $this->assertTrue(function_exists('money_formatter'));
    }

    public function test_rate_formatter_exists(): void
    {
        $this->assertTrue(function_exists('rate_formatter'));
    }

    public function test_ali_sign_url_exists(): void
    {
        $this->assertTrue(function_exists('ali_sign_url'));
    }

    public function test_admin_can_exists(): void
    {
        $this->assertTrue(function_exists('admin_can'));
    }

    public function test_admin_cannot_exists(): void
    {
        $this->assertTrue(function_exists('admin_cannot'));
    }

    // -------------------------------------------------------
    // Basic behavior: admin_path
    // -------------------------------------------------------

    public function test_admin_path_returns_string(): void
    {
        $result = admin_path();

        $this->assertIsString($result);
    }

    public function test_admin_path_with_subpath(): void
    {
        $result = admin_path('Controllers');

        $this->assertStringEndsWith('Controllers', $result);
    }

    // -------------------------------------------------------
    // Basic behavior: admin_base_path
    // -------------------------------------------------------

    public function test_admin_base_path_returns_string(): void
    {
        $result = admin_base_path();

        $this->assertIsString($result);
    }

    public function test_admin_base_path_with_path(): void
    {
        $result = admin_base_path('users');

        $this->assertStringEndsWith('/users', $result);
    }

    public function test_admin_base_path_returns_slash_when_no_prefix_and_no_path(): void
    {
        config(['admin.route.prefix' => '']);

        $result = admin_base_path();

        $this->assertSame('/', $result);
    }

    // -------------------------------------------------------
    // Basic behavior: format_byte
    // -------------------------------------------------------

    public function test_format_byte_bytes(): void
    {
        $this->assertSame('100B', format_byte(100));
    }

    public function test_format_byte_kilobytes(): void
    {
        $this->assertSame('2KB', format_byte(2048));
    }

    public function test_format_byte_megabytes(): void
    {
        $this->assertSame('2MB', format_byte(2 * 1024 * 1024));
    }

    public function test_format_byte_zero(): void
    {
        $this->assertSame('0B', format_byte(0));
    }

    // -------------------------------------------------------
    // Basic behavior: money_formatter
    // -------------------------------------------------------

    public function test_money_formatter_basic(): void
    {
        $this->assertSame('1.00', money_formatter(100));
    }

    public function test_money_formatter_null_returns_zero(): void
    {
        $this->assertSame('0.00', money_formatter(null));
    }

    public function test_money_formatter_empty_string_returns_zero(): void
    {
        $this->assertSame('0.00', money_formatter(''));
    }

    public function test_money_formatter_custom_decimals(): void
    {
        $this->assertSame('1.500', money_formatter(150, 3));
    }

    // -------------------------------------------------------
    // Basic behavior: rate_formatter
    // -------------------------------------------------------

    public function test_rate_formatter_basic(): void
    {
        $this->assertSame('1.000', rate_formatter(100));
    }

    public function test_rate_formatter_null_returns_zero(): void
    {
        $this->assertSame('0.000', rate_formatter(null));
    }

    public function test_rate_formatter_empty_string_returns_zero(): void
    {
        $this->assertSame('0.000', rate_formatter(''));
    }

    // -------------------------------------------------------
    // Basic behavior: ali_sign_url
    // -------------------------------------------------------

    public function test_ali_sign_url_blank_path_returns_empty(): void
    {
        $this->assertSame('', ali_sign_url(null));
        $this->assertSame('', ali_sign_url(''));
    }

    // -------------------------------------------------------
    // Basic behavior: admin_extension_path
    // -------------------------------------------------------

    public function test_admin_extension_path_returns_string(): void
    {
        $result = admin_extension_path();

        $this->assertIsString($result);
    }

    public function test_admin_extension_path_with_subpath(): void
    {
        $result = admin_extension_path('vendor/package');

        $this->assertStringEndsWith('vendor/package', $result);
    }
}
