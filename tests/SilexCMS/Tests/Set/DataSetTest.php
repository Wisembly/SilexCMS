<?php

namespace SilexCMS\Tests\Set;

use Symfony\Component\HttpFoundation\Request;

use SilexCMS\Page\StaticPage;
use SilexCMS\Set\DataSet;

use SilexCMS\Tests\Base;

class DataSetTest extends Base
{
    public function testRegisterAndRender()
    {
        $app = $this->createApplication();

        $app->register(new DataSet('letters', 'digits')); // I LIED !
        $app->register(new StaticPage('static_page', '/dataset', 'dataset.html.twig'));

        $request = Request::create('/dataset');
        $response = $app->handle($request);

        $this->assertEquals('123', $response->getContent());
    }

    public function testRegisterAndLazyLoading()
    {
        $app = $this->createApplication();

        $app->register(new DataSet('letters', 'letters'));
        $app->register(new StaticPage('static_page', '/dataset', 'foo.html.twig'));

        $app->handle(Request::create('/dataset'));

        $this->assertTrue(!isset($app['letters']));
    }
}
