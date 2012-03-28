<?php

namespace SilexCMS;

use Silex\Application as BaseApplication;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\DoctrineServiceProvider;
use Silex\Provider\FormServiceProvider;
use Silex\Provider\SymfonyBridgesServiceProvider;
use Silex\Provider\TranslationServiceProvider;

class Application extends BaseApplication
{
    public function __construct($values)
    {
        parent::__construct();
        
        foreach ($values as $key => $value) {
            $this[$key] = $value;
        }
        
        $this->register(new TwigServiceProvider());
        $this->register(new DoctrineServiceProvider());
        $this->register(new FormServiceProvider());
        $this->register(new SymfonyBridgesServiceProvider());
        $this->register(new TranslationServiceProvider());
    }
}
