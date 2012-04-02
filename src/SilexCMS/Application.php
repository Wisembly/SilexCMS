<?php

namespace SilexCMS;

use Silex\Application as BaseApplication;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\DoctrineServiceProvider;
use Silex\Provider\FormServiceProvider;
use Silex\Provider\SymfonyBridgesServiceProvider;
use Silex\Provider\TranslationServiceProvider;
use Silex\Provider\ValidatorServiceProvider;
use Silex\Provider\SessionServiceProvider;

class Application extends BaseApplication
{
    public function __construct($values)
    {
        parent::__construct();
        
        foreach ($values as $key => $value) {
            $this[$key] = $value;
        }
        
        $this->register(new TwigServiceProvider());
        $this->register(new SymfonyBridgesServiceProvider());
        $this->register(new DoctrineServiceProvider());
        $this->register(new FormServiceProvider());
        $this->register(new TranslationServiceProvider());
        $this->register(new ValidatorServiceProvider());
        $this->register(new SessionServiceProvider());
    }
}
