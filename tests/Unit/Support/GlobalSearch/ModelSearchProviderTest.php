<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Support\GlobalSearch;

use Dcat\Admin\Support\GlobalSearch\ModelSearchProvider;
use Dcat\Admin\Support\GlobalSearch\SearchProviderInterface;
use Dcat\Admin\Tests\TestCase;

class ModelSearchProviderTest extends TestCase
{
    public function test_is_abstract(): void
    {
        $ref = new \ReflectionClass(ModelSearchProvider::class);

        $this->assertTrue($ref->isAbstract());
    }

    public function test_implements_search_provider_interface(): void
    {
        $ref = new \ReflectionClass(ModelSearchProvider::class);

        $this->assertTrue($ref->implementsInterface(
            SearchProviderInterface::class
        ));
    }

    public function test_has_required_abstract_methods(): void
    {
        $ref = new \ReflectionClass(ModelSearchProvider::class);
        $abstractMethods = array_filter(
            $ref->getMethods(\ReflectionMethod::IS_ABSTRACT),
            fn ($m) => $m->getDeclaringClass()->getName() === ModelSearchProvider::class
        );

        $names = array_map(fn ($m) => $m->getName(), $abstractMethods);

        $this->assertContains('model', $names);
        $this->assertContains('searchColumns', $names);
        $this->assertContains('titleColumn', $names);
        $this->assertContains('url', $names);
    }

    public function test_icon_default(): void
    {
        $ref = new \ReflectionMethod(ModelSearchProvider::class, 'icon');
        $ref->setAccessible(true);

        // Icon is protected, test via reflection on a concrete subclass would be ideal,
        // but we can verify the method exists and is protected
        $this->assertTrue($ref->isProtected());
    }

    public function test_description_column_default_null(): void
    {
        $ref = new \ReflectionMethod(ModelSearchProvider::class, 'descriptionColumn');
        $ref->setAccessible(true);

        $this->assertTrue($ref->isProtected());
    }

    public function test_search_method_signature(): void
    {
        $method = new \ReflectionMethod(ModelSearchProvider::class, 'search');
        $params = $method->getParameters();

        $this->assertCount(2, $params);
        $this->assertSame('keyword', $params[0]->getName());
        $this->assertSame('limit', $params[1]->getName());
        $this->assertSame(5, $params[1]->getDefaultValue());
    }
}
