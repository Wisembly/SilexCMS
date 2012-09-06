<?php

namespace SilexCMS\Response;

use Silex\Application;
use Silex\ServiceProviderInterface;

class TemplateLoader implements ServiceProviderInterface
{
    private $name;
    private $app;

    public function __construct($name)
    {
        $this->name = $name;
    }

    public function boot(Application $app)
    {
    }

    public function register(Application $app)
    {
        $this->app = $app;
        $app[$this->name] = $this;
    }

    public function load($template)
    {
        try {
            $this->app['twig']->getLoader()->getSource($template);
        } catch (\Exception $e) {
            $template = __DIR__ . '/../Resources/views/' . $template;
        }

        return $template;
    }
}