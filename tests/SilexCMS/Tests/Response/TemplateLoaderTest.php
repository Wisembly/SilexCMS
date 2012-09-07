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
        $app = $this->getApplication();

        $app::loadCore($app);

        $this->assertEquals('foo.html.twig', $app['silexcms.template.loader']->load('foo.html.twig'));
        $this->assertTrue(false !== strpos($app['silexcms.template.loader']->load('not_foo.html.twig'), 'SilexCMS/src/SilexCMS/Response/../Resources/views/not_foo.html.twig'));
    }
}