<?php

namespace Dcat\Admin\Tests\Unit\Exception;

use Dcat\Admin\Contracts\ExceptionHandler;
use Dcat\Admin\Exception\Handler;
use Dcat\Admin\Tests\TestCase;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;

class ExceptionHandlerTest extends TestCase
{
    public function test_handler_implements_exception_handler_interface(): void
    {
        $handler = new Handler;

        $this->assertInstanceOf(ExceptionHandler::class, $handler);
    }

    public function test_handler_has_handle_method(): void
    {
        $this->assertTrue(method_exists(Handler::class, 'handle'));
    }

    public function test_handler_has_render_method(): void
    {
        $this->assertTrue(method_exists(Handler::class, 'render'));
    }

    public function test_handler_has_report_method(): void
    {
        $this->assertTrue(method_exists(Handler::class, 'report'));
    }

    public function test_handle_rethrows_http_response_exception(): void
    {
        $handler = new Handler;
        $httpException = new HttpResponseException(new Response('test'));

        $this->expectException(HttpResponseException::class);
        $handler->handle($httpException);
    }

    public function test_render_rethrows_in_debug_mode(): void
    {
        $this->app['config']->set('app.debug', true);
        $handler = new Handler;
        $exception = new \RuntimeException('Test error');

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Test error');
        $handler->render($exception);
    }

    public function test_replace_base_path_strips_base(): void
    {
        $handler = new Handler;
        $ref = new \ReflectionMethod($handler, 'replaceBasePath');
        $ref->setAccessible(true);

        $basePath = str_replace('\\', '/', base_path().'/');
        $result = $ref->invoke($handler, $basePath.'app/Models/User.php');

        $this->assertEquals('app/Models/User.php', $result);
    }

    public function test_replace_base_path_handles_backslashes(): void
    {
        $handler = new Handler;
        $ref = new \ReflectionMethod($handler, 'replaceBasePath');
        $ref->setAccessible(true);

        $basePath = base_path().'\\';
        $result = $ref->invoke($handler, $basePath.'app\\Models\\User.php');

        $this->assertEquals('app/Models/User.php', $result);
    }
}
