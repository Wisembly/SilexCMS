<?php

namespace SilexCMS\Tests\Page;

use Symfony\Component\HttpFoundation\Request;

use SilexCMS\Page\StaticPage;
use SilexCMS\Set\DataSet;
use SilexCMS\Tests\Base;

class StaticPageTest extends Base
{
    public function testSolo()
    {
        $app = $this->createApplication();
        $app->register(new StaticPage('staticpage', '/toto', $this->getTemplateStream('Foobar')));

        $this->assertEquals('Foobar', $app->handle(Request::create('/toto'))->getContent());
    }

    public function testAdvanced()
    {
        $app = $this->createApplication();
        $app->register(new DataSet('books', 'book'));
        $app->register(new DataSet('categories', 'category'));
        $app->register(new StaticPage('staticpage', '/toto', 'advanced_static_page.html.twig'));

        $this->assertEquals('Lord Of The Rings - sci-fi', $app->handle(Request::create('/toto'))->getContent());
    }
}
