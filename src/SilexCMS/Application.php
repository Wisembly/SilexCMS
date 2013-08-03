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
use SilexCMS\Security\Firewall;
use SilexCMS\Security\SecurityController;
use SilexCMS\Administration\AdministrationController;
use SilexCMS\Response\TemplateLoader;
use SilexCMS\Cache\CacheManager;

use Symfony\Component\HttpFoundation\Request;

class Application extends BaseApplication
{
    public function __construct(array $values)
    {
        parent::__construct();

        $this->before(function (Request $request) {
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

        $this->register(new TemplateLoader('silexcms.template.loader'));
    }

    public function loadCore(array $options = array())
    {
        // security
        if (isset($options['security'])) {
            $this->register(new Firewall('silexcms.security', $options['security']));
            $this->register(new SecurityController());
            $this->register(new AdministrationController($this['db']));
        }

        // caching strategy
        if (isset($options['cache'])) {
            $this->register(new CacheManager('silexcms.cache.manager', array(
                'active'    => !$this['debug'],
                'type'      => isset($options['cache']['type']) ? $options['cache']['type'] : 'array',
            )));

            $this->before(function(Request $request) {
                return $this['silexcms.cache.manager']->check($request);
            });

            $this->after(function(Request $request, $response) {
                $this['silexcms.cache.manager']->persist($request, $response);
            });
        }

        return $this;
    }
}
