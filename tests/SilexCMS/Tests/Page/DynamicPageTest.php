<?php

namespace SilexCMS\Tests\Page;

use Symfony\Component\HttpFoundation\Request;

use SilexCMS\Page\DynamicPage;

use SilexCMS\Tests\Base;

class DynamicPageTest extends Base
{
    public function testSolo()
    {
        $app = $this->createApplication();
        $app->register(new DynamicPage('dynamic_page', '/test/{val}', 'test_dynamic_page.html.twig', 'digits'));
        $this->assertEquals('1', $app->handle(Request::create('/test/1'))->getContent());

        $app = $this->createApplication();
        $app->register(new DynamicPage('dynamic_page', '/test/{val}', 'test_dynamic_page.html.twig', 'digits'));
        $this->assertEquals('2', $app->handle(Request::create('/test/2'))->getContent());
    }

    public function testWrongRepoInDebugMode()
    {
        $app = $this->createApplication();
        $app->register(new DynamicPage('dynamic_page', '/foo/{val}', 'syntax_error_template.html.twig', 'notfound'));

        $this->assertContains('SQLSTATE[HY000]: General error: 1 no such table: notfound', $app->handle(Request::create('/foo/1'))->getContent());
    }

    public function testWrongRepoInProductionMode()
    {
        $app = $this->createApplication(array('debug' => false));
        $app->register(new DynamicPage('dynamic_page', '/foo/{val}', 'syntax_error_template.html.twig', 'notfound'));

        $this->assertEquals(500, $app->handle(Request::create('/foo/1'))->getStatusCode());
    }

    public function testWrongRepoKeyInDebugMode()
    {
        $app = $this->createApplication();
        $app->register(new DynamicPage('dynamic_page', '/foo/{notfound}', 'syntax_error_template.html.twig', 'digits'));

        $this->assertContains('SQLSTATE[HY000]: General error: 1 no such column: notfound', $app->handle(Request::create('/foo/1'))->getContent());
    }

    public function testWrongRepoKeyInProductionMode()
    {
        $app = $this->createApplication(array('debug' => false));
        $app->register(new DynamicPage('dynamic_page', '/foo/{notfound}', 'syntax_error_template.html.twig', 'digits'));

        $this->assertEquals(500, $app->handle(Request::create('/foo/1'))->getStatusCode());
    }

    public function testWrongTemplateInDebugMode()
    {
        $app = $this->createApplication();
        $app->register(new DynamicPage('dynamic_page', '/foo/{val}', 'syntax_error_template.html.twig', 'digits'));

        $this->assertContains('Calling "parent" outside a block is forbidden in "syntax_error_template.html.twig" at line 1', $app->handle(Request::create('/foo/1'))->getContent());
    }

    public function testWrongTemplateInProdutionMode()
    {
        $app = $this->createApplication(array('debug' => false));
        $app->register(new DynamicPage('dynamic_page', '/foo/{val}', 'syntax_error_template.html.twig', 'digits'));

        $this->assertEquals(500, $app->handle(Request::create('/foo/2'))->getStatusCode());
    }
}
