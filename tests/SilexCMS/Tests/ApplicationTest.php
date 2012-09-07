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
}
