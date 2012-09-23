<?php

namespace SilexCMS\Tests\Security;

use SilexCMS\Tests\Base;
use SilexCMS\Application;
use Symfony\Component\HttpFoundation\Request;

class securityTest extends Base
{
    public function testLogin()
    {
        $app = $this->createApplication();
        $app = Application::loadCore($app, array('security' => array('username' => 'password')));

        $client = $app->handle(Request::create('/login'));
        $this->assertEquals(200, $client->getStatusCode());
    }
}