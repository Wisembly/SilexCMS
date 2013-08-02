<?php

namespace SilexCMS\Page;

use Silex\Application;
use Silex\ServiceProviderInterface;

use SilexCMS\Response\TransientResponse;

class StaticPage extends Page implements ServiceProviderInterface
{
    public function register(Application $app)
    {
        $name = $this->name;
        $route = $this->route;
        $template = $this->template;

        $app->get($route, function (Application $app) use ($name, $route, $template) {

            try {
                $response = new TransientResponse($app, $template);
            } catch (\Exception $e) {
                return $this->handleException($e->getMessage());
            }

            return $response;
        })->bind($name);
    }
}
