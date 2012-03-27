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
    public function __construct($block, $table = null)
    {
        if (is_null($table)) {
            $table = $block;
        }
        
        $this->block = $block;
        $this->table = $table;
    }
    
    public function register(Application $app)
    {
        $self = $this;
        
        $this->app = $app;
        
        $app->after(function (Request $req, Response $resp) use ($self, $app) {
            $self->filter($resp);
        });
    }
    
    public function filter(Response $resp)
    {
        if ($resp instanceof TransientResponse) {
            if ($resp->getTemplate()->hasBlock($this->block)) {
                $repository = new GenericRepository($this->app['db'], $this->table);
                $resp->getVariables()->{$this->block} = $this->app[$this->block] = $repository->findAll();
            }
        }
    }
}
