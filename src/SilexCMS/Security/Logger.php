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

    public function bindUser($username)
    {
        $this->app['session']->set('username', $this->username = $username);

        return $this;
    }

    public function bindSession()
    {
        return $this->bindUser($this->app['session']->get('username'));
    }

    public function bindRequest(Request $req)
    {
        $username = $req->get('_username');
        $password = $req->get('_password');

        return $this->bindUser($this->service->check($username, $password) ? $username : null);
    }

    public function unbind()
    {
        return $this->bindUser(null);
    }

    public function getUsername()
    {
        return $this->username;
    }

}
