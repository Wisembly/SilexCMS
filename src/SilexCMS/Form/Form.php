<?php

namespace SilexCMS\Form;

use Silex\Application;
use Silex\ServiceProviderInterface;

class Form implements ServiceProviderInterface
{
    private $name;
    private $form;
    
    public function __construct($name, $form = null)
    {
        if (is_null($form)) {
            $form = $name . '.yml';
        }
        
        if (!($form instanceof \stdClass)) {
            $form = new YamlForm($name, $form);
        }
        
        $this->name = $name;
        $this->form = $form;
    }
    
    public function register(Application $app)
    {
        $object = $app[$this->name . '.object'] = $this->form->createObject();
        $form = $app[$this->name . '.form'] = $app['form.factory']->createBuilder($this->form, $object)->getForm();
        $view = $app[$this->name . '.view'] = $form->createView();
    }
}
