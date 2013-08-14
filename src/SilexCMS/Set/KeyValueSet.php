<?php

namespace SilexCMS\Set;

use Silex\Application;
use Silex\ServiceProviderInterface;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use SilexCMS\Repository\GenericRepository;
use SilexCMS\Response\TransientResponse;

class KeyValueSet implements ServiceProviderInterface
{
    private $key;
    private $app;
    private $block;
    private $table;

    public function __construct($block, $table, $key)
    {
        $this->key = $key;
        $this->block = $block;
        $this->table = $table;
    }

    public function boot(Application $app) {}

    public function register(Application $app)
    {
        $self = $this;
        $this->app = $app;

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

    public function getSet()
    {
        $repository = new GenericRepository($this->app['db'], $this->table);
        $values = $repository->findAll();

        if (!empty($values) && !isset($values[0][$this->key])) {
            throw new \Exception("You must provide a valid key, '{$this->key}' is not");
        }

        foreach ($values as $key => $value) {
            $newKey = $value[$this->key];
            unset($value[$this->key]);

            if (1 === count($value)) {
                $value = array_pop($value);
            }

            $values[$newKey] = $value;
            unset($values[$key]);
        }

        return $values;
    }
}
