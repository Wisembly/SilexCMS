<?php

namespace SilexCMS\Set;

use Silex\Application;
use Silex\ServiceProviderInterface;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use SilexCMS\Repository\GenericRepository;
use SilexCMS\Response\TransientResponse;

class DataSet implements ServiceProviderInterface
{
    private $block;
    private $table;

    public function __construct($block, $table, $conditions = null)
    {
        $this->block = $block;
        $this->table = $table;
        $this->conditions = $conditions;
    }

    public function boot(Application $app) {}

    public function register(Application $app)
    {
        $self = $this;

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
                $repository = new GenericRepository($app['db'], $this->table);
                $app[$this->block] = $repository->select($this->conditions);
            }
        }
    }
}
