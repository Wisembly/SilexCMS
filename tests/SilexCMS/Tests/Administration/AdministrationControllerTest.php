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
        $this->assertContains('letters', $client->getContent());
        $this->assertContains('digits', $client->getContent());
        $this->assertContains('category', $client->getContent());
        $this->assertContains('book', $client->getContent());
    }

    public function testAdministrationTable()
    {
        $client = $this->app->handle(Request::create('/administration/book'));
        $this->assertEquals('200', $client->getStatusCode());
        $this->assertContains('Lord Of The Rings', $client->getContent());
        $this->assertContains('Dune', $client->getContent());

        // mapped foreign keys
        $this->assertContains('sci-fi', $client->getContent());
        $this->assertContains('fantasy', $client->getContent());
    }

    // public function testAdministrationRow()
    // {
    //     $client = $this->app->handle(Request::create('/administration/book/1'));
    //     $this->assertEquals('200', $client->getStatusCode());
    //     $this->assertContains('Lord Of The Rings', $client->getContent());
    // }
}