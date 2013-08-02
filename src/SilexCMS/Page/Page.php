<?php

namespace SilexCMS\Page;

use Silex\Application;

class Page
{
    protected $name;
    protected $route;
    protected $template;
    protected $app;

    public function __construct($name, $route, $template)
    {
        $this->name = $name;
        $this->route = $route;
        $this->template = $template;
    }

    public function boot(Application $app)
    {
        $this->app = $app;
    }

    protected function handleException($message)
    {
        if (!$this->app['debug']) {
            throw new \Exception($message);
        }

        return $message;
    }
}
