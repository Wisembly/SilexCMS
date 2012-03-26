<?php

namespace SilexCMS\Page;

use Silex\Application;
use Silex\ServiceProviderInterface;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use SilexCMS\Response\TransientResponse;

class StaticPage implements ServiceProviderInterface
{
    public function __construct($route, $template)
    {
        $this->route = $route;
        
        $this->template = $template;
    }
    
    public function register(Application $app)
    {
        $self = $this;
        
        $app->get($this->route, function (Application $app, Request $req) use ($self) {
            return new TransientResponse($app['twig'], $self->template);
        });
    }
}
