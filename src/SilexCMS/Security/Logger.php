<?php

namespace SilexCMS\Security;

use Symfony\Component\HttpFoundation\Request;

use Silex\Application;

class Logger
{
    private $service;
    private $app;
    private $username;
    
    public function __construct($service, Application $app)
    {
        $this->service = $service;
        $this->app = $app;
    }
    
    public function bindSession()
    {
        $this->username = $this->app['session']->get('username');
        
        return $this;
    }
    
    public function bindRequest(Request $req)
    {
        $username = $req->get('_username');
        $password = $req->get('_password');
        
        if ($this->service->check($username, $password)) {
            $this->app['session']->set('username', $this->username = $username);
        }
        
        return $this;
    }
    
    public function getUsername()
    {
        return $this->username;
    }
    
}
