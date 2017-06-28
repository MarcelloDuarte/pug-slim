<?php

namespace Md\Pug\Tests;

use Md\Pug\PugRenderer;
use Slim\App;

abstract class AbstractTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @var App
     */
    protected $app;

    /**
     * @var PugRenderer
     */
    protected $pug;

    public function setUp()
    {
        $app = new App(array(
            'version'        => '0.0.0',
            'debug'          => false,
            'mode'           => 'testing',
            'templates.path' => __DIR__ . '/templates'
        ));

        $container = $app->getContainer();
        $container['renderer'] = new PugRenderer($container['templates.path']);

        $app->get('/hello/{name}', function ($request, $response, $args) {
            return $this->renderer->render($response, '/home.pug', $args);
        });

        $this->app = $app;
        $this->pug = $container['renderer'];
    }
}