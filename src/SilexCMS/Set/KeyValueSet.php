<?php

namespace SilexCMS\Set;

use Silex\Application;
use Silex\ServiceProviderInterface;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use SilexCMS\Repository\GenericRepository;
use SilexCMS\Response\TransientResponse;

use SilexCMS\Set\SetInteface;

class KeyValueSet implements ServiceProviderInterface, SetInterface
{
    private $block;
    private $table;
    private $repository;
    private $primaryKey;

    public function __construct($block, $table, $primaryKey)
    {
        $this->block = $block;
        $this->table = $table;
        $this->primaryKey = $primaryKey;
    }

    public function boot(Application $app) {}

    public function getRepository()
    {
        return $this->repository;
    }

    public function registerSet(Application $app)
    {
        $app['silexcms.sets'] = array_merge($app['silexcms.sets'], array($this->table => $this));
    }

    public function register(Application $app)
    {
        $self = $this;
        $this->repository = new GenericRepository($app['db'], $this->table, $this->primaryKey);
        $this->registerSet($app);

        // since 5 nov 2012 (see changelog..) after and before event changed priorities..
        // Set up prioirity to 8, and it just works fine..
        $app->after(function (Request $req, Response $resp) use ($self, $app) {
            $self->filter($app, $resp);
        }, 8);
    }

    /**
    * [ 'key' => 'foo', 'value' => 'bar' ] => [ 'foo' => 'bar' ]
    * [ 'key' => 'foo', 'value' => 'bar', 'value2' => 'baz' ] => [ 'foo' => [ 'value' => 'bar', 'value2' => 'baz' ] ]
    */
    public function filter(Application $app, Response $resp)
    {
        if ($resp instanceof TransientResponse) {
            if ($resp->getTemplate()->hasBlock($this->block)) {
                $app[$this->block] = $this->getSet();
            }
        }
    }

    public function getSet($mappForeign = false)
    {
        $values = $this->repository->findAll($mappForeign);

        if (!empty($values) && !isset($values[0][$this->primaryKey])) {
            throw new \Exception("You must provide a valid key, '{$this->primaryKey}' is not");
        }

        foreach ($values as $key => $value) {
            $newKey = $value[$this->primaryKey];
            unset($value[$this->primaryKey]);

            if (1 === count($value)) {
                $value = array_pop($value);
            }

            $values[$newKey] = $value;
            unset($values[$key]);
        }

        return $values;
    }
}
