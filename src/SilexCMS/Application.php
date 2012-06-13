<?php

namespace SilexCMS;

use Silex\Application as BaseApplication;
use Silex\Provider\DoctrineServiceProvider;
use Silex\Provider\FormServiceProvider;
use Silex\Provider\SessionServiceProvider;
use Silex\Provider\TranslationServiceProvider;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\UrlGeneratorServiceProvider;
use Silex\Provider\ValidatorServiceProvider;

class Application extends BaseApplication
{
    public function __construct($values)
    {
        parent::__construct();
        
        foreach ($values as $key => $value) {
            $this[$key] = $value;
        }

        $this->before(function ($request) {
            $request->getSession()->start();
        });
        
        $this->register(new SessionServiceProvider());
        $this->register(new TwigServiceProvider());
        $this->register(new DoctrineServiceProvider());
        $this->register(new UrlGeneratorServiceProvider());
        $this->register(new TranslationServiceProvider());
        $this->register(new FormServiceProvider());
        $this->register(new ValidatorServiceProvider());
    }
}
