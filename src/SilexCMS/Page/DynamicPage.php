<?php

namespace SilexCMS\Page;

use Silex\Application;
use Silex\ServiceProviderInterface;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use SilexCMS\Response\TransientResponse;

class DynamicPage implements ServiceProviderInterface
{
    public function __construct($route, $table, $template = null)
    {
        $this->route = $route;
        
        $this->table = $table;
        
        $this->template = !is_null($template) ? $template : $table . '.html.twig';
    }
    
    public function register(Application $app)
    {
        $self = $this;
        
        $app->get($this->route, function (Application $app, Request $req) use ($self) {
            $response = new TransientResponse($app['twig'], $self->template);
            
            $repository = new GenericRepository($app['db'], $self->table);
            $response->getVariables()->set = null;
            
            return $response;
        });
    }
}
