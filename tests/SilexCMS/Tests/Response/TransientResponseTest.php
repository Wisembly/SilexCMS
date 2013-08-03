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

    public function testWrongLoadFromString()
    {
        $app = $this->createApplication();

        $app->get('/file', function (Application $app) {
            try {
                return new TransientResponse($app, 'this is an unsupported string');
            } catch (\Exception $e) {
                return $e->getMessage();
            }
        });

        $this->assertEquals('Template "this is an unsupported string" is not defined ().', $app->handle(Request::create('/file'))->getContent());
    }

    public function testLoadWithSyntaxError()
    {
        $app = $this->createApplication();

        $app->get('/stream', function (Application $app) {
            try {
                return new TransientResponse($app, 'syntax_error_template.html.twig');
            } catch (\Twig_Error_Syntax $e) {
                return $e->getMessage();
            }
        });

        $this->assertEquals('Calling "parent" outside a block is forbidden in "syntax_error_template.html.twig" at line 1', $app->handle(Request::create('/stream'))->getContent());
    }

    public function testLoadWithTemplateVariableErrorInDebugMode()
    {
        $app = $this->createApplication();

        $app->get('/stream', function (Application $app) {
            return new TransientResponse($app, 'missing_var_template.html.twig');
        });

        $this->assertContains('Variable "bar" does not exist in "missing_var_template.html.twig" at line 1', $app->handle(Request::create('/stream'))->getContent());
    }

    public function testLoadWithTemplateVariableErrorInProductionMode()
    {
        $app = $this->createApplication(array('debug' => false));

        $app->get('/stream', function (Application $app) {
            return new TransientResponse($app, 'missing_var_template.html.twig');
        });

        $this->assertEquals('foo ', $app->handle(Request::create('/stream'))->getContent());
    }
}
