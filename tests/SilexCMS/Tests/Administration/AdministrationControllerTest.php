<?php

namespace SilexCMS\Tests\Administration;

use SilexCMS\Tests\Base;
use Symfony\Component\HttpFoundation\Request;

use SilexCMS\Set\DataSet;

class AdministrationControllerTest extends Base
{
    protected $app;

    public function setUp()
    {
        $this->app = $this->createApplication(array('silexcms.security' => array('user' => 'pass')));
        $this->app['silexcms.security']->bindUser('user');
    }

    public function testAdministrationAccessRestricted()
    {
        $this->app['silexcms.security']->bindUser(null);
        $this->app->register(new DataSet('book', 'book'));

        $client = $this->app->handle(Request::create('/administration'));
        $this->assertEquals(302, $client->getStatusCode());

        $client = $this->app->handle(Request::create('/administration/book'));
        $this->assertEquals(302, $client->getStatusCode());

        $client = $this->app->handle(Request::create('/administration/book/1'));
        $this->assertEquals(302, $client->getStatusCode());
    }

    public function testAdministrationHub()
    {
        $this->app->register(new DataSet('letters', 'letters'));
        $this->app->register(new DataSet('book', 'book'));
        $client = $this->app->handle(Request::create('/administration'));
        $this->assertEquals(200, $client->getStatusCode());

        // we have these two registered sets
        $this->assertContains('letters', $client->getContent());
        $this->assertContains('book', $client->getContent());

        // but not these ones, even if they exist in DB
        $this->assertFalse(strpos($client->getContent(), 'digits'));
        $this->assertFalse(strpos($client->getContent(), 'category'));
    }

    public function testAdministrationTable()
    {
        $this->app->register(new DataSet('book', 'book'));
        $client = $this->app->handle(Request::create('/administration/book'));

        $this->assertEquals(200, $client->getStatusCode());
        $this->assertContains('Lord Of The Rings', $client->getContent());
        $this->assertContains('Dune', $client->getContent());

        // mapped foreign keys
        $this->assertContains('sci-fi', $client->getContent());
        $this->assertContains('fantasy', $client->getContent());
    }

    public function testAdministrationRow()
    {
        $this->app->register(new DataSet('book', 'book'));
        $client = $this->app->handle(Request::create('/administration/book/1'));

        $this->assertEquals(200, $client->getStatusCode());
        $this->assertContains('Lord Of The Rings', $client->getContent());
        $this->assertContains('sci-fi', $client->getContent());
    }

    public function testAdministrationRowEdit()
    {
        $this->app->register(new DataSet('book', 'book'));
        $client = $this->app->handle(Request::create('/administration/book/1', 'POST', array(
            'TableBook' => array(
                'row' => array(
                    0 => array(
                        'name'          => 'foo',
                        'category_id'   => 2,
                    ),
                ),
            ),
        )));

        $this->assertEquals(200, $client->getStatusCode());
        $this->assertContains('foo', $client->getContent());
        $this->assertContains('fantasy', $client->getContent());
    }

    public function testAdministrationRowNew()
    {
        $this->app->register(new DataSet('book', 'book'));
        $client = $this->app->handle(Request::create('/administration/book/_new', 'POST', array(
            'TableBook' => array(
                'row' => array(
                    0 => array(
                        'name'          => 'bar',
                        'category_id'   => 2,
                    ),
                ),
            ),
        )));

        $this->assertEquals(302, $client->getStatusCode());
    }
}