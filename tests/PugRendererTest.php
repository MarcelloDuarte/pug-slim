<?php

namespace Md\Pug\Tests;

use InvalidArgumentException;
use Md\Pug\PugRenderer;
use Pug\Pug;
use RuntimeException;
use Slim\Http\Body;
use Slim\Http\Headers;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Http\Stream;
use Slim\Http\Uri;

class PugRendererTest extends AbstractTestCase
{
    public function testPugRenderer()
    {
        $tempIn = sys_get_temp_dir() . '/streamIn.txt';
        $tempOut = sys_get_temp_dir() . '/streamIn.txt';
        touch($tempIn);
        touch($tempOut);
        $headers = new Headers();
        $uri = Uri::createFromString('/home/bob');
        $body = new Stream(fopen($tempIn, 'r'));
        $container = $this->app->getContainer();
        $request = new Request('GET', $uri, $headers, [], [], $body);
        /** @var \Slim\Router $router */
        $router = $container->get('router');
        $route = $router->lookupRoute('route0');
        $route->prepare($request, [
            'name' => 'bob',
        ]);
        $response = new Response(200, null, new Body(fopen($tempOut, 'w')));
        $route->run($request, $response);

        self::assertSame(
            '<!DOCTYPE html><html>'.
            '<head><title>Home page</title></head>'.
            '<body><header><h1>Home page</h1></header><section>Hello bob</section><footer>Bye</footer></body>'.
            '</html>',
            file_get_contents($tempOut)
        );
    }

    public function testTemplateNotFound()
    {
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('View cannot render `foo.pug` because the template does not exist');

        $this->pug->fetch('foo.pug');
    }

    public function testGetTemplatePath()
    {
        $path = rtrim($this->pug->getTemplatePath(), DIRECTORY_SEPARATOR);

        self::assertSame($path, $this->app->getContainer()['templates.path']);
    }

    public function testForbiddenKey()
    {
        self::expectException(InvalidArgumentException::class);
        self::expectExceptionMessage('Duplicate template key found');

        $this->pug->fetch('foo.pug', [
            'template' => 'bar.pug',
        ]);
    }

    public function testAttributes()
    {
        $this->pug->setAttributes([
            'foo' => 'bar',
        ]);
        $this->pug->addAttribute('biz', 42);

        self::assertSame([
            'foo' => 'bar',
            'biz' => 42,
        ], $this->pug->getAttributes());
        self::assertSame('bar', $this->pug->getAttribute('foo'));
        self::assertSame(42, $this->pug->getAttribute('biz'));
        self::assertSame(false, $this->pug->getAttribute('bar'));
    }

    public function testAdaptee()
    {
        $renderer = new PugRenderer(__DIR__, [
            'cache'         => 'foo',
            'upToDateCheck' => false,
        ]);

        self::assertInstanceOf(Pug::class, $renderer->adaptee);
        self::assertSame('foo', $renderer->adaptee->getOption('cache'));
        self::assertFalse($renderer->adaptee->getOption('upToDateCheck'));
    }
}
