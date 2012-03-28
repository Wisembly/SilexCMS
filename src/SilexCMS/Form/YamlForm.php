<?php

namespace SilexCMS\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Yaml\Yaml;

class YamlForm extends AbstractType
{
    private $name;
    private $data;
    
    public function __construct($name, $path)
    {
        $this->name = $name;
        $this->data = Yaml::parse($path);
    }
    
    public function getName()
    {
        return $this->name;
    }
    
    public function createObject()
    {
        $object = new \stdClass();
        $this->buildObject($object);
        
        return $object;
    }
    
    public function buildObject($object)
    {
        foreach ($this->data as $widget => $parameters) {
            $object->{$widget} = null;
        }
    }
    
    public function buildForm(FormBuilder $builder, array $options)
    {
        foreach ($this->data as $widget => $parameters) {
            $type = isset($parameters['type']) ? $parameters['type'] : null;
            $attributes = isset($parameters['attributes']) ? $parameters['attributes'] : array();
            $builder->add($widget, $type, $attributes);
        }
    }
}
