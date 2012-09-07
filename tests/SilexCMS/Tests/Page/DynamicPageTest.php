<?php

namespace SilexCMS\Tests\Page;

use Symfony\Component\HttpFoundation\Request;

use SilexCMS\Page\DynamicPage;

use SilexCMS\Tests\Base;

class DynamicPageTest extends Base
{
    public function testSolo()
    {
        $app = $this->getApplication();
        $app->register(new DynamicPage('dynamic_page', '/test/{val}', 'test_dynamic_page.html.twig', 'digits'));
        $this->assertEquals('1', $app->handle(Request::create('/test/1'))->getContent());

        $app = $this->getApplication();
        $app->register(new DynamicPage('dynamic_page', '/test/{val}', 'test_dynamic_page.html.twig', 'digits'));
        $this->assertEquals('2', $app->handle(Request::create('/test/2'))->getContent());
    }
}
