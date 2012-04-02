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
        $app = $this->getApplication();
        
        $app->get('/file', function (Application $app) {
            return new TransientResponse($app['twig'], 'composer.json');
        });
        
        $this->assertEquals(file_get_contents('composer.json'), $app->handle(Request::create('/file'))->getContent());
    }
    
    public function testLoadFromStream()
    {
        $app = $this->getApplication();
        
        $app->get('/stream', function (Application $app) {
            $stream = fopen('data://text/plain,Foobar', 'r');
            return new TransientResponse($app['twig'], $stream);
        });
        
        $this->assertEquals('Foobar', $app->handle(Request::create('/stream'))->getContent());
    }
}
