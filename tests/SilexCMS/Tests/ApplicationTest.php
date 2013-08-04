<?php

namespace SilexCMS\Tests;

use SilexCMS\Application;

class ApplicationTest extends \PHPUnit_Framework_TestCase
{
    private $app;

    public function setUp()
    {
        $this->app = new Application(array());
    }

    public function testInstanceOf()
    {
        $this->assertInstanceOf('\\Silex\\Application', $this->app);
    }

    public function testHasTwig()
    {
        $this->assertTrue(isset($this->app['twig']));
    }

    public function testHasFormFactory()
    {
        $this->assertTrue(isset($this->app['form.factory']));
    }

    public function testHasDB()
    {
        $this->assertTrue(isset($this->app['db']));
    }

    public function testHasTranslator()
    {
        $this->assertTrue(isset($this->app['translator']));
    }

    public function testHasValidator()
    {
        $this->assertTrue(isset($this->app['validator']));
    }

    public function testHasSession()
    {
        $this->assertTrue(isset($this->app['session']));
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
}
