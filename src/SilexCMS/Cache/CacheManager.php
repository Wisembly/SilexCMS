<?php

namespace SilexCMS\Cache;

use Silex\Application;
use Silex\ServiceProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CacheManager implements ServiceProviderInterface
{
    private $name;
    private $app;
    private $manager;

    private $type = 'array';
    private $active = true;
    private $version;
    private $toBeCached = array();

    const VERSION_KEY = 'silexcms.cache.version';
    const VERSION_DATE_KEY = 'silexcms.cache.version.date';

    public function __construct($name, array $options = array())
    {
        $this->name = $name;

        foreach ($options as $option => $value) {
            $this->$option = $value;
        }
    }

    public function boot(Application $app)
    {
        $type = 'Doctrine\Common\Cache\\' . ucfirst($this->type) . 'Cache';
        $this->manager = new $type;
        $this->manager->setNamespace('silexcms.cache');
        $this->version = $this->getCacheVersion();
    }

    public function register(Application $app)
    {
        $this->app = $app;
        $app[$this->name] = $this;
    }

    public function activate()
    {
        $this->active = true;
    }

    public function deActivate()
    {
        $this->active = false;
    }

    public function check(Request $request)
    {
        if (!$this->active) {
            return;
        }

        if ($this->isFresh($request)) {
            $response = new Response($this->getCachedVersion($request));
            $response->headers->set('SilexCMS-Cached-At', $this->getCacheVersionDate());
            $response->headers->set('SilexCMS-Cache-Version', $this->getCacheVersion());

            return $response;
        }

        return;
    }

    public function persist(Request $request, $response)
    {
        if (!$this->active) {
            return;
        }

        $url = $request->server->get('REQUEST_URI');

        if (isset($this->toBeCached[$url])) {
            $this->manager->save($url, $response->getContent());
            $this->manager->save($url . '_version', $this->version);
        }
    }

    public function update()
    {
        if (empty($this->version)) {
            $this->version = $this->getCacheVersion();
        }

        $this->manager->save(self::VERSION_KEY, ++$this->version);
        $this->saveVersionDate();
    }

    public function getCacheVersionDate()
    {
        if (false === $this->manager->contains(self::VERSION_DATE_KEY)) {
            $date = new \DateTime();

            return $date->format('c');
        }

        return $this->manager->fetch(self::VERSION_DATE_KEY)->format('c');
    }

    public function getCacheVersion()
    {
        if (false === $this->manager->contains(self::VERSION_KEY)) {
            $this->version = 1;
            $this->manager->save(self::VERSION_KEY, $this->version);
            $this->saveVersionDate();

            return $this->version;
        }

        return $this->manager->fetch(self::VERSION_KEY);
    }

    private function saveVersionDate()
    {
        $this->manager->save(self::VERSION_DATE_KEY, new \DateTime());
    }

    private function isFresh(Request $request)
    {
        $url = $request->server->get('REQUEST_URI');

        if (false !== $this->manager->contains($url) && $this->version === $this->manager->fetch($url . '_version')) {
            return true;
        }

        $this->toBeCached[$url] = true;

        return false;
    }

    private function getCachedVersion(Request $request)
    {
        return $this->manager->fetch($request->server->get('REQUEST_URI'));
    }
}