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
    private $options;

    /**
    *   mixed $options either an array or yaml valid ressource
    */
    public function __construct($options)
    {
        parent::__construct();
        $this->options = $options;

        // if options is a yaml file, read it
        if (!is_array($options) && false !== strpos($options, '.yml')) {
            $this->readYamlConfig($options);
        }

        // tweak $options and register SilexCMS twig path
        $this->registerTwigService();

        $this->register(new DoctrineServiceProvider(),     $this->options);
        $this->register(new TranslationServiceProvider(),  $this->options);
        $this->register(new SessionServiceProvider(),      $this->options);
        $this->register(new FormServiceProvider(),         $this->options);
        $this->register(new ValidatorServiceProvider(),    $this->options);
        $this->register(new UrlGeneratorServiceProvider(), $this->options);

        $this->register(new TemplateLoader('silexcms.template.loader'));

        // security
        if (isset($options['silexcms.security'])) {
            $this->registerSecurityService();
        }

        // caching strategy
        if (isset($this->options['silexcms.cache'])) {
            $this->registerCacheService();
        }

        // handle errors and exceptions in debug mode
        $this->registerErrorHandlers();

        // registering console and console commands
        $this->registerConsoleService();

        // registered sets container
        $this['silexcms.sets'] = array();
    }

    private function readYamlConfig($yamlPath)
    {
        $config = Yaml::parse($yamlPath);

        if (!is_array($config)) {
            throw new \Exception('Wrong yaml file');
        }

        $this->options = $config;
    }

    private function registerTwigService()
    {
        $twigSilexCMSPath = __DIR__ . '/Resources/views';

        if (isset($this->options['twig.path'])) {
            if (is_array($this->options['twig.path'])) {
                $this->options['twig.path'][] = $twigSilexCMSPath;
            } else {
                $this->options['twig.path'] = array($this->options['twig.path'], $twigSilexCMSPath);
            }
        } else {
            $this->options['twig.path'] = $twigSilexCMSPath;
        }

        $this->register(new TwigServiceProvider(), $this->options);
    }

    private function registerCacheService()
    {
        $this->register(new CacheManager('silexcms.cache.manager', array(
            'active'    => isset($this->options['silexcms.cache']['enable']) ? $this->options['silexcms.cache']['enable'] : false,
            'type'      => isset($this->options['silexcms.cache']['type']) ? $this->options['silexcms.cache']['type'] : 'array',
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

    private function registerSecurityService()
    {
        $this->register(new Firewall('silexcms.security', $this->options['silexcms.security']));
        $this->register(new SecurityController());
        $this->register(new AdministrationController($this['db']));
    }

    private function registerErrorHandlers()
    {
        ErrorHandler::register($this['debug']);
        ExceptionHandler::register($this['debug']);
    }

    private function registerConsoleService()
    {
        $this->register(new ConsoleServiceProvider(), array(
            'console.name'              => 'SilexCMS',
            'console.version'           => '1.0.0',
            'console.project_directory' => __DIR__ . '/..',
        ));
        $this['console']->add(new ClearCacheCommand());
    }
}
