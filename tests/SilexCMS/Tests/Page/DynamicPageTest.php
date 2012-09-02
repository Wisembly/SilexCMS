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
        $stream = $this->getTemplateStream('{{ app.set[0].val }}');
        $app->register(new DynamicPage('dynpage1', '/test/{val}', $stream, 'digits'));
        $this->assertEquals('1', $app->handle(Request::create('/test/1'))->getContent());

        $app = $this->getApplication();
        $stream = $this->getTemplateStream('{{ app.set[0].val }}');
        $app->register(new DynamicPage('dynpage2', '/test/{val}', $stream, 'digits'));
        $this->assertEquals('2', $app->handle(Request::create('/test/2'))->getContent());
    }
}
