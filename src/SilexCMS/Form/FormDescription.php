<?php

namespace SilexCMS\Form;

use Silex\Application;
use Silex\ServiceProviderInterface;

use Symfony\Component\Form\AbstractType;

class FormDescription implements ServiceProviderInterface
{
    private $name;
    private $type;

    public function __construct($name, AbstractType $type)
    {
        $this->name = $name;
        $this->type = $type;
    }

    public function boot(Application $app)
    {
    }

    public function register(Application $app)
    {
        $name = $this->name;
        $type = $this->type;

        $app[$name] = function ($app) use ($name, $type) {
            return $app['form.factory']->createBuilder($type)->getForm();
        };

        $app[$name . '_view'] = function ($app) use ($name, $type) {
            return $app[$name]->createView();
        };
    }
}
