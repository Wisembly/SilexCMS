<?php

namespace SilexCMS\Tests\Twig\Extension;

use Symfony\Component\HttpFoundation\Request;

use SilexCMS\Page\StaticPage;
use SilexCMS\Set\DataSet;
use SilexCMS\Tests\Base;

class MapExtensionTest extends Base
{
    public function testMapSuccess()
    {
        $app = $this->createApplication();
        $app['twig']->addExtension(new \SilexCMS\Twig\Extension\MapExtension($app));

        $app->register(new DataSet('map', 'map'));
        $app->register(new StaticPage('staticpage', '/foo', 'map.html.twig'));

        $this->assertEquals('bar - baz', $app->handle(Request::create('/foo'))->getContent());
    }

    public function testForeignFailure()
    {
        $app = $this->createApplication();
        $app['twig']->addExtension(new \SilexCMS\Twig\Extension\MapExtension($app));

        $app->register(new DataSet('map', 'map'));
        $app->register(new StaticPage('staticpage', '/foo', 'map_fail.html.twig'));

        $this->assertContains("Could not map with 'plop' key", $app->handle(Request::create('/foo'))->getContent());
    }
}
