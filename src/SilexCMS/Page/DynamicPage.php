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
    private $name;
    private $route;
    private $template;
    private $table;

    public function __construct($name, $route, $template, $table)
    {
        $this->name = $name;
        $this->route = $route;
        $this->template = $template;
        $this->table = $table;
    }

    public function boot(Application $app)
    {
    }

    public function register(Application $app)
    {
        $name = $this->name;
        $route = $this->route;
        $template = $this->template;
        $table = $this->table;

        $app->get($route, function (Application $app, Request $req, $_route_params) use ($name, $route, $template, $table) {
            $repository = new GenericRepository($app['db'], $table);
            $app['silexcms.dynamic.route'] = array('name' => $name, 'route' => $route, 'table' => $table, 'route_params' => $_route_params);
            $app['set'] = $repository->findOneBy($_route_params);
            $response = new TransientResponse($app['twig'], $template);
            return $response;
        })->bind($name);
    }
}
