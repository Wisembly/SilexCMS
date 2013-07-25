<?php

namespace SilexCMS\Page;

use Silex\Application;
use Silex\ServiceProviderInterface;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use SilexCMS\Response\TransientResponse;

class StaticPage implements ServiceProviderInterface
{
    private $name;
    private $route;
    private $template;

    public function __construct($name, $route, $template)
    {
        $this->name = $name;
        $this->route = $route;
        $this->template = $template;
    }

    public function boot(Application $app)
    {
    }

    public function register(Application $app)
    {
        $name = $this->name;
        $route = $this->route;
        $template = $this->template;

        $app->get($route, function (Application $app, Request $req) use ($name, $route, $template) {
            return new TransientResponse($app, $template);
        })->bind($name);
    }
}
