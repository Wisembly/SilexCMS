<?php

namespace SilexCMS\Security;

use Silex\Application;
use Silex\ServiceProviderInterface;

use SilexCMS\Security\Logger;

class Firewall implements ServiceProviderInterface
{
    private $name;
    private $store;
    private $username;
    
    public function __construct($name, $store)
    {
        $this->name = $name;
        $this->store = $store;
        $this->username = null;
    }
    
    public function boot(Application $app)
    {
    }
    
    public function register(Application $app)
    {
        $logger = $app[$this->name] = new Logger($this, $app);
        
        $app->before(function () use ($logger) {
            $logger->bindSession();
        });
    }
    
    public function check($username, $password)
    {
        return isset($this->store[$username]) && $this->store[$username] === $password;
    }
}
