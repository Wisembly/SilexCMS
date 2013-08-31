<?php

namespace SilexCMS\Tests\Set;

use SilexCMS\Set\LocalizationSet;

use SilexCMS\Tests\Base;

class LocalizationSetTest extends Base
{
    public function testTranslator()
    {
        $app = $this->createApplication();

        $set = new LocalizationSet('map', 'map', 'key');
        $app->register($set);
        $set->injectLocalizations();

        $this->assertEquals('bar', $app['translator']->trans('foo'));
        $this->assertEquals('qux', $app['translator']->trans('qux'));
    }
}
