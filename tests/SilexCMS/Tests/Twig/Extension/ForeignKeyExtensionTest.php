<?php

namespace SilexCMS\Tests\Twig\Extension;

use Symfony\Component\HttpFoundation\Request;

use SilexCMS\Page\StaticPage;
use SilexCMS\Set\DataSet;
use SilexCMS\Tests\Base;

class StaticPageTest extends Base
{
    public function testSolo()
    {
        $app = $this->createApplication();
        $app['twig']->addExtension(new \SilexCMS\Twig\Extension\ForeignKeyExtension($app));

        $app->register(new DataSet('books', 'book'));
        $app->register(new DataSet('categories', 'category'));
        $app->register(new StaticPage('staticpage', '/foo', 'foreign.html.twig'));

        $this->assertEquals('fantasy', $app->handle(Request::create('/foo'))->getContent());
    }
}
