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
use Knp\Provider\ConsoleServiceProvider;

use Symfony\Component\Debug\ErrorHandler;
use Symfony\Component\Debug\ExceptionHandler;

use SilexCMS\Command\ClearCacheCommand;

use Symfony\Component\Yaml\Yaml;
use Symfony\Component\HttpFoundation\Request;

class Application extends BaseApplication
{
    /**
    *   mixed $options either an array or yaml valid ressource
    */
    public function __construct($options)
    {
        parent::__construct();

        // if options is a yaml file, read it
        if (!is_array($options) && false !== strpos($options, '.yml')) {
            $options = Yaml::parse($options);

            if (!is_array($options)) {
                throw new \Exception('Wrong yaml file');
            }
        }

        $this->register(new SessionServiceProvider(),      $options);
        $this->register(new TwigServiceProvider(),         $options);
        $this->register(new DoctrineServiceProvider(),     $options);
        $this->register(new UrlGeneratorServiceProvider(), $options);
        $this->register(new TranslationServiceProvider(),  $options);
        $this->register(new FormServiceProvider(),         $options);
        $this->register(new ValidatorServiceProvider(),    $options);

        $this->register(new TemplateLoader('silexcms.template.loader'));

        $this->register(new ConsoleServiceProvider(), array(
            'console.name'              => 'SilexCMS',
            'console.version'           => '1.0.0',
            'console.project_directory' => __DIR__ . '/..',
        ));

        // security
        if (isset($options['silexcms.security'])) {
            $this->register(new Firewall('silexcms.security', $options['silexcms.security']));
            $this->register(new SecurityController());
            $this->register(new AdministrationController($this['db']));
        }

        // caching strategy
        if (isset($options['silexcms.cache'])) {
            $this->register(new CacheManager('silexcms.cache.manager', array(
                'active'    => !$this['debug'],
                'type'      => isset($options['silexcms.cache']['type']) ? $options['silexcms.cache']['type'] : 'array',
            )));

            $this->before(function(Request $request) {
                // only cache GET requests
                if ('GET' !== $request->getMethod()) {
                    return;
                }

                return $this['silexcms.cache.manager']->check($request);
            }, BaseApplication::EARLY_EVENT);

            $this->after(function(Request $request, $response) {
                $this['silexcms.cache.manager']->persist($request, $response);
            });
        }

        // handle errors and exceptions in debug mode
        ErrorHandler::register($this['debug']);
        ExceptionHandler::register($this['debug']);

        // registering console commands
        $this['console']->add(new ClearCacheCommand());
    }
}
