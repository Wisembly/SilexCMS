<?php

namespace SilexCMS\Tests\Page;

use Symfony\Component\HttpFoundation\Request;

use SilexCMS\Page\StaticPage;

use SilexCMS\Tests\Base;

class StaticPageTest extends Base
{
    public function testSolo()
    {
        $stream = $this->getTemplateStream('Foobar');
        
        $app = $this->getApplication();
        $app->register(new StaticPage('/toto', $stream));
        
        $this->assertEquals('Foobar', $app->handle(Request::create('/toto'))->getContent());
    }
}
