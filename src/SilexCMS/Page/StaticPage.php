<?php

namespace SilexCMS\Page;

use Silex\Application;
use Silex\ServiceProviderInterface;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use SilexCMS\Response\TransientResponse;

class StaticPage implements ServiceProviderInterface
{
    private $route;
    private $template;
    
    public function __construct($route, $template)
    {
        $this->route = $route;
        $this->template = $template;
    }
    
    public function register(Application $app)
    {
        $thisAccessor = $this; // php 5.3 workaround
        
        $app->get($this->route, function (Application $app, Request $req) use ($thisAccessor) {
            return new TransientResponse($app['twig'], $thisAccessor->template);
        });
    }
}
