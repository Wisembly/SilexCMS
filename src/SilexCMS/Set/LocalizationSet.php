<?php

namespace SilexCMS\Set;

use Silex\Application;
use SilexCMS\Set\LocalizationsSet;
use Silex\ServiceProviderInterface;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use SilexCMS\Repository\GenericRepository;
use SilexCMS\Response\TransientResponse;

class LocalizationSet extends KeyValueSet implements ServiceProviderInterface, SetInterface
{
    // load KeyValueSet data into Translator
    public function filter(Application $app, Response $resp)
    {
        if ($resp instanceof TransientResponse) {
            if ($resp->getTemplate()->hasBlock($this->block)) {
                $this->injectLocalizations();
            }
        }
    }

    public function injectLocalizations()
    {
        $this->app['translator']->addResource('array', $this->getSet(), $this->app['translator']->getLocale());
    }
}
