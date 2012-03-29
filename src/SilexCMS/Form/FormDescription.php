<?php

namespace SilexCMS\Form;

use Silex\Application;
use Silex\ServiceProviderInterface;

use Symfony\Component\Form\AbstractType;

class FormDescription implements ServiceProviderInterface
{
    private $name;
    private $builder;
    
    public function __construct($name, AbstractType $builder)
    {
        $this->name = $name;
        $this->builder = $builder;
    }
    
    public function register(Application $app)
    {
        $form = $app[$this->name] = $app['form.factory']->createBuilder($this->builder)->getForm();
        $view = $app[$this->name . '_view'] = $form->createView();
    }
}
