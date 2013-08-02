<?php

namespace SilexCMS\Tests\Cache;

use Symfony\Component\HttpFoundation\Request;

use SilexCMS\Page\StaticPage;
use SilexCMS\Set\DataSet;

use SilexCMS\Tests\Base;

class CacheManagerTest extends Base
{
    public function testWithoutCache()
    {
        $app = $this->createApplication();

        $app['db']->executeQuery('CREATE TABLE messages (id int, value char)');
        $app['db']->insert('messages', array('id' => 0, 'value' => 'bar'));
        $app->register(new DataSet('messages', 'messages'));
        $app->register(new StaticPage('static_page', '/foo', 'cache_page.html.twig'));

        $response = $app->handle(Request::create('/foo'));
        $this->assertEquals('bar', $response->getContent());

        $app['db']->update('messages', array('value' => 'baz'), array('id' => 0));
        $response = $app->handle(Request::create('/foo'));
        $this->assertEquals('baz', $response->getContent());
    }

    public function testWithCache()
    {
        $app = $this->createApplication(false);
        $app->loadCore(array(
            'cache' => array(
                'type'  => 'array',
            ),
        ));

        $app['db']->executeQuery('CREATE TABLE messages (id int, value char)');
        $app['db']->insert('messages', array('id' => 0, 'value' => 'bar'));
        $app->register(new DataSet('messages', 'messages'));
        $app->register(new StaticPage('static_page', '/foo', 'cache_page.html.twig'));

        $response = $app->handle(Request::create('/foo'));
        $this->assertEquals('bar', $response->getContent());

        $app['db']->update('messages', array('value' => 'baz'), array('id' => 0));
        $response = $app->handle(Request::create('/foo'));
        $this->assertEquals('bar', $response->getContent());

        $app['silexcms.cache.manager']->update();
        $response = $app->handle(Request::create('/foo'));
        $this->assertEquals('baz', $response->getContent());
    }

    public function testCacheDeActivation()
    {
        $app = $this->createApplication(false);
        $app->loadCore(array(
            'cache' => array(
                'type'  => 'array',
            ),
        ));

        $app['db']->executeQuery('CREATE TABLE messages (id int, value char)');
        $app['db']->insert('messages', array('id' => 0, 'value' => 'bar'));
        $app->register(new DataSet('messages', 'messages'));
        $app->register(new StaticPage('static_page', '/foo', 'cache_page.html.twig'));

        $response = $app->handle(Request::create('/foo'));
        $this->assertEquals('bar', $response->getContent());

        $app['db']->update('messages', array('value' => 'baz'), array('id' => 0));
        $response = $app->handle(Request::create('/foo'));
        $this->assertEquals('bar', $response->getContent());

        $app['silexcms.cache.manager']->deActivate();
        $response = $app->handle(Request::create('/foo'));
        $this->assertEquals('baz', $response->getContent());

        $app['silexcms.cache.manager']->activate();
        $response = $app->handle(Request::create('/foo'));
        $this->assertEquals('bar', $response->getContent());
    }
}
