<?php

namespace SilexCMS\Tests\Set;

use Symfony\Component\HttpFoundation\Request;

use SilexCMS\Page\StaticPage;
use SilexCMS\Set\KeyValueSet;

use SilexCMS\Tests\Base;

class KeyValueSetTest extends Base
{
    public function testWrongKeyValueSet()
    {
        $app = $this->createApplication();

        $app->register(new KeyValueSet('map', 'map', 'wrongkey'));
        $app->register(new StaticPage('static_page', '/keyvalueset', 'key_value_set.html.twig'));

        $response = $app->handle(Request::create('/keyvalueset'));

        $this->assertContains("You must provide a valid key, 'wrongkey' is not", $response->getContent());
    }

    public function testKeyValueSet()
    {
        $app = $this->createApplication();

        $app->register(new KeyValueSet('map', 'map', 'key'));
        $app->register(new StaticPage('static_page', '/keyvalueset', 'key_value_set.html.twig'));

        $response = $app->handle(Request::create('/keyvalueset'));

        $this->assertEquals('bar - baz', $response->getContent());
    }

    public function testKeyValuesSet()
    {
        $app = $this->createApplication();

        $app->register(new KeyValueSet('map2', 'map2', 'key'));
        $app->register(new StaticPage('static_page', '/keyvaluesset', 'key_values_set.html.twig'));

        $response = $app->handle(Request::create('/keyvaluesset'));

        $this->assertEquals('bar - qux', $response->getContent());
    }
}
