<?php

namespace SilexCMS\Tests\Security;

use SilexCMS\Tests\Base;
use SilexCMS\Application;
use Symfony\Component\HttpFoundation\Request;

class SecurityControllerTest extends Base
{
    public function testLogin()
    {
        $app = $this->createApplication(array('silexcms.security' => array('user' => 'pass')));

        $client = $app->handle(Request::create('/login'));
        $this->assertEquals(200, $client->getStatusCode());
    }

    public function testWrongCredentials()
    {
        $app = $this->createApplication(array('silexcms.security' => array('user' => 'pass')));

        $client = $app->handle(Request::create('/login', 'POST', array('_username' => 'foo', '_password' => 'bar')));
        $this->assertEquals(200, $client->getStatusCode());
        $this->assertArrayHasKey('error', $client->getVariables());
    }

    public function testCredentials()
    {
        $app = $this->createApplication(array('silexcms.security' => array('user' => 'pass')));

        $client = $app->handle(Request::create('/login', 'POST', array('_username' => 'user', '_password' => 'pass')));
        $this->assertEquals(302, $client->getStatusCode());
        $this->assertEquals('/administration', $client->getTargetUrl());

        // try to go again, still connected
        $client = $app->handle(Request::create('/login'));
        $this->assertEquals(302, $client->getStatusCode());
        $this->assertEquals('/administration', $client->getTargetUrl());

        // try to logout
        $client = $app->handle(Request::create('/logout'));
        $this->assertEquals(302, $client->getStatusCode());
        $this->assertEquals('/login', $client->getTargetUrl());

        // try to go again, now disconnected
        $client = $app->handle(Request::create('/login'));
        $this->assertEquals(200, $client->getStatusCode());
    }
}