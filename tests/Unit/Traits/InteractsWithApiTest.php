<?php

namespace Dcat\Admin\Tests\Unit\Traits;

use Dcat\Admin\Tests\TestCase;
use Dcat\Admin\Traits\InteractsWithApi;
use Mockery;

class InteractsWithApiTestHelper
{
    use InteractsWithApi;
}

class InteractsWithApiTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_trait_exists(): void
    {
        $this->assertTrue(trait_exists(InteractsWithApi::class));
    }

    public function test_default_method_is_post(): void
    {
        $helper = new InteractsWithApiTestHelper;

        $this->assertSame('POST', $helper->getRequestMethod());
    }

    public function test_parameters_returns_empty_array(): void
    {
        $helper = new InteractsWithApiTestHelper;

        $this->assertSame([], $helper->parameters());
    }

    public function test_get_request_method_returns_method(): void
    {
        $helper = new InteractsWithApiTestHelper;

        $this->assertSame('POST', $helper->getRequestMethod());
    }

    public function test_get_uri_key_returns_class_name(): void
    {
        $helper = new InteractsWithApiTestHelper;

        $this->assertSame(InteractsWithApiTestHelper::class, $helper->getUriKey());
    }

    public function test_click_adds_selector(): void
    {
        $helper = new InteractsWithApiTestHelper;

        $result = $helper->click('.btn');

        $this->assertSame($helper, $result);
        $this->assertContains('.btn', $helper->getRequestSelectors());
    }

    public function test_click_adds_multiple_selectors(): void
    {
        $helper = new InteractsWithApiTestHelper;

        $helper->click('.btn1');
        $helper->click('.btn2');

        $selectors = $helper->getRequestSelectors();
        $this->assertContains('.btn1', $selectors);
        $this->assertContains('.btn2', $selectors);
        $this->assertCount(2, $selectors);
    }

    public function test_click_accepts_array(): void
    {
        $helper = new InteractsWithApiTestHelper;

        $helper->click(['.a', '.b']);

        $this->assertCount(2, $helper->getRequestSelectors());
    }

    public function test_get_request_selectors_returns_array(): void
    {
        $helper = new InteractsWithApiTestHelper;

        $this->assertIsArray($helper->getRequestSelectors());
        $this->assertSame([], $helper->getRequestSelectors());
    }

    public function test_fetching_adds_script(): void
    {
        $helper = new InteractsWithApiTestHelper;

        $result = $helper->fetching('console.log("fetching")');

        $this->assertSame($helper, $result);

        $scripts = $helper->getRequestScripts();
        $this->assertContains('console.log("fetching")', $scripts['fetching']);
    }

    public function test_fetched_adds_script(): void
    {
        $helper = new InteractsWithApiTestHelper;

        $result = $helper->fetched('console.log("fetched")');

        $this->assertSame($helper, $result);

        $scripts = $helper->getRequestScripts();
        $this->assertContains('console.log("fetched")', $scripts['fetched']);
    }

    public function test_allow_build_request_false_without_url(): void
    {
        $helper = new InteractsWithApiTestHelper;

        $this->assertFalse($helper->allowBuildRequest());
    }

    public function test_get_request_scripts_returns_array_with_keys(): void
    {
        $helper = new InteractsWithApiTestHelper;

        $scripts = $helper->getRequestScripts();

        $this->assertIsArray($scripts);
        $this->assertArrayHasKey('fetching', $scripts);
        $this->assertArrayHasKey('fetched', $scripts);
        $this->assertSame([], $scripts['fetching']);
        $this->assertSame([], $scripts['fetched']);
    }

    public function test_merge_copies_selectors_and_scripts(): void
    {
        $source = new InteractsWithApiTestHelper;
        $source->click('.source-btn');
        $source->fetching('source_fetching()');
        $source->fetched('source_fetched()');

        $target = new InteractsWithApiTestHelper;
        $result = $target->merge($source);

        $this->assertSame($target, $result);
        $this->assertContains('.source-btn', $target->getRequestSelectors());

        $scripts = $target->getRequestScripts();
        $this->assertContains('source_fetching()', $scripts['fetching']);
        $this->assertContains('source_fetched()', $scripts['fetched']);
    }
}
