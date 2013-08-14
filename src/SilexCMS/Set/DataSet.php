<?php

namespace SilexCMS\Set;

use Silex\Application;
use Silex\ServiceProviderInterface;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use SilexCMS\Repository\GenericRepository;
use SilexCMS\Response\TransientResponse;

use SilexCMS\Set\SetInteface;

class DataSet implements ServiceProviderInterface, SetInterface
{
    private $block;
    private $table;
    private $primaryKey;

    public function __construct($block, $table, $conditions = null, $primaryKey = 'id')
    {
        $this->block = $block;
        $this->table = $table;
        $this->primaryKey = $primaryKey;
        $this->conditions = $conditions;
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
        $this->registerSet($app);
        $this->repository = new GenericRepository($app['db'], $this->table, $this->primaryKey);

        // since 5 nov 2012 (see changelog..) after and before event changed priorities..
        // Set up prioirity to 8, and it just works fine..
        $app->after(function (Request $req, Response $resp) use ($self, $app) {
            $self->filter($app, $resp);
        }, 8);
    }

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
        return $this->repository->select($this->conditions, $mappForeign);
    }
}
