<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Support;

use Dcat\Admin\Exception\AdminException;
use Dcat\Admin\Support\ClassSigner;
use Dcat\Admin\Tests\TestCase;

class ClassSignerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->app['config']->set('app.key', 'base64:test-key-for-signing-1234567890abc=');
    }

    public function test_sign_returns_class_with_signature(): void
    {
        $signed = ClassSigner::sign('App\\Actions\\MyAction');

        $this->assertStringContainsString('|', $signed);
        $this->assertStringStartsWith('App\\Actions\\MyAction|', $signed);
    }

    public function test_verify_returns_class_name_for_valid_signature(): void
    {
        $signed = ClassSigner::sign('App\\Actions\\MyAction');
        $class = ClassSigner::verify($signed);

        $this->assertSame('App\\Actions\\MyAction', $class);
    }

    public function test_verify_throws_on_tampered_class(): void
    {
        $signed = ClassSigner::sign('App\\Actions\\MyAction');
        $tampered = 'App\\Actions\\EvilAction|'.explode('|', $signed)[1];

        $this->expectException(AdminException::class);
        $this->expectExceptionMessage('Class signature verification failed.');
        ClassSigner::verify($tampered);
    }

    public function test_verify_throws_on_tampered_signature(): void
    {
        $signed = ClassSigner::sign('App\\Actions\\MyAction');
        $tampered = explode('|', $signed)[0].'|invalidsignature';

        $this->expectException(AdminException::class);
        $this->expectExceptionMessage('Class signature verification failed.');
        ClassSigner::verify($tampered);
    }

    public function test_verify_falls_back_for_unsigned_class(): void
    {
        $class = ClassSigner::verify('App\\Actions\\MyAction');

        $this->assertSame('App\\Actions\\MyAction', $class);
    }

    public function test_verify_falls_back_for_empty_string(): void
    {
        $class = ClassSigner::verify('');

        $this->assertSame('', $class);
    }

    public function test_sign_produces_consistent_output(): void
    {
        $signed1 = ClassSigner::sign('App\\Actions\\MyAction');
        $signed2 = ClassSigner::sign('App\\Actions\\MyAction');

        $this->assertSame($signed1, $signed2);
    }

    public function test_different_classes_produce_different_signatures(): void
    {
        $signed1 = ClassSigner::sign('App\\Actions\\ActionA');
        $signed2 = ClassSigner::sign('App\\Actions\\ActionB');

        $sig1 = explode('|', $signed1)[1];
        $sig2 = explode('|', $signed2)[1];

        $this->assertNotSame($sig1, $sig2);
    }
}
