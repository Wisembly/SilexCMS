<?php

namespace SilexCMS\Tests\Administration;

use SilexCMS\Tests\Base;
use Symfony\Component\HttpFoundation\Request;

class AdministrationControllerTest extends Base
{
    protected $app;

    public function setUp()
    {
        $this->app = $this->createApplication();
        $this->app->loadCore(array('security' => array('user' => 'pass')));
        $this->app->handle(Request::create('/login', 'POST', array('_username' => 'user', '_password' => 'pass')));
    }

    public function testAdministrationHub()
    {
        $client = $this->app->handle(Request::create('/administration'));
        $this->assertEquals('200', $client->getStatusCode());
    }
}