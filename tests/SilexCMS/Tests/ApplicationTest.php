<?php

namespace SilexCMS\Tests;

use SilexCMS\Application;

class ApplicationTest extends Base
{
    private $rawApp;

    public function setUp()
    {
        $this->rawApp = new Application(array());
    }

    public function testInstanceOf()
    {
        $this->assertInstanceOf('\\Silex\\Application', $this->rawApp);
    }

    public function testHasTwig()
    {
        $this->assertTrue(isset($this->rawApp['twig']));
    }

    public function testHasFormFactory()
    {
        $this->assertTrue(isset($this->rawApp['form.factory']));
    }

    public function testHasDB()
    {
        $this->assertTrue(isset($this->rawApp['db']));
    }

    public function testHasTranslator()
    {
        $this->assertTrue(isset($this->rawApp['translator']));
    }

    public function testHasValidator()
    {
        $this->assertTrue(isset($this->rawApp['validator']));
    }

    public function testHasSession()
    {
        $this->assertTrue(isset($this->rawApp['session']));
    }

    public function testWithGoodYaml()
    {
        $app = new Application(__DIR__.'/Resources/config/config.yml');
        $this->assertInstanceOf('\\Silex\\Application', $app);
        $this->assertTrue(isset($app['silexcms.cache.manager']));
        $this->assertTrue(isset($app['silexcms.security']));
    }

    /**
    * @expectedException Exception
    */
    public function testWithWrongYaml()
    {
        $app = new Application(__DIR__.'/Resources/config/wrong_config.yml');
    }

    public function testTranslator()
    {
        $app = $this->createApplication(array('locale' => 'en'));
        $this->assertEquals('Hello', $app['translator']->trans('hello'));

        $app = $this->createApplication(array('locale' => 'fr'));
        $this->assertEquals('Bonjour', $app['translator']->trans('hello'));
    }
}
