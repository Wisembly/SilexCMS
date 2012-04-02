<?php

namespace SilexCMS\Tests\Set;

use Symfony\Component\HttpFoundation\Request;

use SilexCMS\Page\StaticPage;
use SilexCMS\Set\DataSet;

use SilexCMS\Tests\Base;

class DataSetTest extends Base
{
    public function testRegisterAndRenderWithSingleParameter()
    {
        $app = $this->getApplication();
        
        $app->register(new DataSet('letters'));
        $app->register(new StaticPage('/dataset', $this->getTemplateStream('{% block letters %}{% for f in app.letters %}{{ f.val }}{% endfor %}{% endblock %}')));
        
        $this->assertEquals('abc', $app->handle(Request::create('/dataset'))->getContent());
    }
    
    public function testRegisterAndRenderWithOptionalParameter()
    {
        $app = $this->getApplication();
        
        $app->register(new DataSet('letters', 'digits')); // I LIED !
        $app->register(new StaticPage('/dataset', $this->getTemplateStream('{% block letters %}{% for f in app.letters %}{{ f.val }}{% endfor %}{% endblock %}')));
        
        $request = Request::create('/dataset');
        $response = $app->handle($request);
        
        $this->assertEquals('123', $response->getContent());
    }
    
    public function testRegisterAndLazyLoading()
    {
        $app = $this->getApplication();
        
        $app->register(new DataSet('letters'));
        $app->register(new StaticPage('/dataset', $this->getTemplateStream('there is no block level')));
        
        $app->handle(Request::create('/dataset'));
        
        $this->assertTrue(!isset($app['letters']));
    }
}
