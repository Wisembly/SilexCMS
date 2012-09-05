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

        $this->before(function ($request) {
            if (!$request->hasSession()) {
                $request->getSession()->start();
            }
        });

        $this->register(new SessionServiceProvider(),      $values);
        $this->register(new TwigServiceProvider(),         $values);
        $this->register(new DoctrineServiceProvider(),     $values);
        $this->register(new UrlGeneratorServiceProvider(), $values);
        $this->register(new TranslationServiceProvider(),  $values);
        $this->register(new FormServiceProvider(),         $values);
        $this->register(new ValidatorServiceProvider(),    $values);
    }

    public static function loadActions($app)
    {
        require_once __DIR__ . '/Security/security.php';
        require_once __DIR__ . '/Admin/administration.php';
    }
}
