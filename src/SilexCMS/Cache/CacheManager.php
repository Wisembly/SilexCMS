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
        $this->version = $this->getVersion();
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
            return new Response($this->getCachedVersion($request), 403);
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
            $this->version = $this->getVersion();
        }

        $this->version++;
        $this->manager->save('version', $this->version);
    }

    private function getVersion()
    {
        if (false !== $this->manager->contains('version')) {
            $version = $this->manager->fetch('version');
        } else {
            $version = 0;
            $this->manager->save('version', $version);
        }

        return $version;
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