<?php

namespace SilexCMS\Page;

use Silex\Application;
use Silex\ServiceProviderInterface;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use SilexCMS\Response\TransientResponse;

class DynamicPage implements ServiceProviderInterface
{
    private $route;
    private $table;
    private $template;
    
    public function __construct($route, $table, $template = null)
    {
        $this->route = $route;
        $this->table = $table;
        $this->template = !is_null($template) ? $template : $table . '.html.twig';
    }
    
    public function register(Application $app)
    {
        $templateAccessor =& $self->template;
        $tableAccessor =& $self->table;
        
        $app->get($this->route, function (Application $app, Request $req) use (&$templateAccessor, &$tableAccessor) {
            $response = new TransientResponse($app['twig'], $templateAccessor);
            $repository = new GenericRepository($app['db'], $tableAccessor);
            $response->getVariables()->set = null;
            
            return $response;
        });
    }
}
