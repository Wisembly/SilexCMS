<?php

namespace SilexCMS\Tests\Response;

use Symfony\Component\HttpFoundation\Request;

use SilexCMS\Application;
use SilexCMS\Response\TransientResponse;

use SilexCMS\Tests\Base;

class TransientResponseTest extends Base
{
    public function testLoadFromFile()
    {
        $app = $this->createApplication();

        $app->get('/file', function (Application $app) {
            return new TransientResponse($app, 'composer.json');
        });

        $this->assertEquals(file_get_contents('composer.json'), $app->handle(Request::create('/file'))->getContent());
    }

    public function testLoadFromTemplate()
    {
        $app = $this->createApplication();

        $app->get('/stream', function (Application $app) {
            return new TransientResponse($app, 'foo.html.twig');
        });

        $this->assertEquals('bar', $app->handle(Request::create('/stream'))->getContent());
    }
}
