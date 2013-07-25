<?php

namespace SilexCMS\Tests\Response;

use Symfony\Component\HttpFoundation\Request;

use SilexCMS\Application;
use SilexCMS\Response\TransientResponse;

use SilexCMS\Tests\Base;

class TemplateLoaderTest extends Base
{
    public function testLoad()
    {
        $app = $this->createApplication();

        $app::loadCore($app);

        $this->assertEquals('foo.html.twig', $app['silexcms.template.loader']->load('foo.html.twig'));
        $this->assertTrue(false !== strpos($app['silexcms.template.loader']->load('hello'), 'SilexCMS/Response/../Resources/views/hello'));
    }
}