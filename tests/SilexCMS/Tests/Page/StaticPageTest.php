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
        $app->register(new StaticPage('staticpage', '/foo', 'foo.html.twig'));

        $this->assertEquals('bar', $app->handle(Request::create('/foo'))->getContent());
    }

    public function testAdvanced()
    {
        $app = $this->createApplication();
        $app->register(new DataSet('books', 'book'));
        $app->register(new DataSet('categories', 'category'));
        $app->register(new StaticPage('staticpage', '/foo', 'advanced_static_page.html.twig'));

        $this->assertEquals('Lord Of The Rings - sci-fi', $app->handle(Request::create('/foo'))->getContent());
    }

    public function testWrongTemplateInDebugMode()
    {
        $app = $this->createApplication();
        $app->register(new StaticPage('staticpage', '/foo', 'syntax_error_template.html.twig'));

        $this->assertEquals('Calling "parent" outside a block is forbidden in "syntax_error_template.html.twig" at line 1', $app->handle(Request::create('/foo'))->getContent());
    }

    public function testWrongTemplateInProdutionMode()
    {
        $app = $this->createApplication(false);
        $app->register(new StaticPage('staticpage', '/foo', 'syntax_error_template.html.twig'));

        $this->assertEquals(500, $app->handle(Request::create('/foo'))->getStatusCode());
    }
}
