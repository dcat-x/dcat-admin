<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Grid\Concerns;

use Dcat\Admin\Grid;
use Dcat\Admin\Grid\Concerns\HasImporter;
use Dcat\Admin\Grid\Importer;
use Dcat\Admin\Grid\Importers\ExcelImporter;
use Dcat\Admin\Tests\TestCase;
use Mockery;

class HasImporterTest extends TestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }

    public function test_grid_uses_has_importer_trait(): void
    {
        $ref = new \ReflectionClass(Grid::class);
        $traits = $this->getAllTraits($ref);

        $this->assertContains(HasImporter::class, $traits);
    }

    public function test_allow_importer_returns_false_initially(): void
    {
        $helper = new HasImporterTestHelper;

        $this->assertFalse($helper->allowImporter());
    }

    public function test_enable_importer_property_default_false(): void
    {
        $helper = new HasImporterTestHelper;
        $ref = new \ReflectionProperty($helper, 'enableImporter');
        $ref->setAccessible(true);

        $this->assertFalse($ref->getValue($helper));
    }

    public function test_importer_property_initially_null(): void
    {
        $helper = new HasImporterTestHelper;
        $ref = new \ReflectionProperty($helper, 'importer');
        $ref->setAccessible(true);

        $this->assertNull($ref->getValue($helper));
    }

    public function test_render_import_button_returns_empty_when_not_allowed(): void
    {
        $helper = new HasImporterTestHelper;

        $this->assertSame('', $helper->renderImportButton());
    }

    public function test_import_method_enables_importer(): void
    {
        $helper = new HasImporterTestHelper;
        $result = $helper->import();

        $this->assertInstanceOf(Importer::class, $result);
        $this->assertTrue($helper->allowImporter());
    }

    public function test_importer_manager_returns_importer_instance(): void
    {
        $helper = new HasImporterTestHelper;
        $manager = $helper->importerManager();

        $this->assertInstanceOf(Importer::class, $manager);
    }

    public function test_importer_manager_returns_same_instance(): void
    {
        $helper = new HasImporterTestHelper;

        $this->assertSame($helper->importerManager(), $helper->importerManager());
    }

    public function test_import_method_signature(): void
    {
        $method = new \ReflectionMethod(HasImporterTestHelper::class, 'import');
        $params = $method->getParameters();

        $this->assertCount(1, $params);
        $this->assertSame('importerDriver', $params[0]->getName());
        $this->assertTrue($params[0]->isDefaultValueAvailable());
        $this->assertNull($params[0]->getDefaultValue());
    }

    public function test_build_importer_config_includes_replay_protection_fields(): void
    {
        // Finding 1: 防重放需要 expires_at。其他字段（user_id、source）依赖
        // 完整 admin auth + request 上下文，单元测试这里只验 expires_at 必然存在。
        $helper = new HasImporterTestHelper;
        // import() 显式启用并设个有 titles 的 driver，避免触发默认 columns 路径。
        $helper->import(ExcelImporter::make()->titles(['name' => 'Name']));

        $config = $helper->buildImporterConfig();

        $this->assertArrayHasKey('expires_at', $config);
        $this->assertGreaterThan(time(), $config['expires_at']);
    }

    private function getAllTraits(\ReflectionClass $ref): array
    {
        $traits = array_keys($ref->getTraits());
        foreach ($ref->getTraits() as $trait) {
            $traits = array_merge($traits, $this->getAllTraits($trait));
        }
        if ($parent = $ref->getParentClass()) {
            $traits = array_merge($traits, $this->getAllTraits($parent));
        }

        return array_unique($traits);
    }
}

class HasImporterTestHelper extends Grid
{
    use HasImporter;

    public function __construct()
    {
        // Skip parent constructor
    }
}
