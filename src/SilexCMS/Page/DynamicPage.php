<?php

namespace SilexCMS\Page;

use Silex\Application;
use Silex\ServiceProviderInterface;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use SilexCMS\Repository\GenericRepository;
use SilexCMS\Response\TransientResponse;

class DynamicPage implements ServiceProviderInterface
{
    private $route;
    private $table;
    private $template;
    
    public function __construct($route, $template, $table)
    {
        $this->route = $route;
        $this->template = $template;
        $this->table = $table;
    }
    
    public function getRoute()
    {
        return $this->route;
    }
    
    public function getTable()
    {
        return $this->table;
    }
    
    public function getTemplate()
    {
        return $this->template;
    }
    
    public function register(Application $app)
    {
        $thisAccessor = $this; // php 5.3 workaround
        
        $app->get($this->getRoute(), function (Application $app, Request $req, $_route_params) use ($thisAccessor) {
            $response = new TransientResponse($app['twig'], $thisAccessor->getTemplate());
            $repository = new GenericRepository($app['db'], $thisAccessor->getTable());
            $app['set'] = $repository->select($_route_params)->fetchAll();
            return $response;
        });
    }
}
