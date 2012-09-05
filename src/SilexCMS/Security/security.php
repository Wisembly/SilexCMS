<?php

use Silex\Application;

use Symfony\Component\HttpFoundation\Request;
use SilexCMS\Response\TransientResponse;

$app->register(new SilexCMS\Security\Firewall('security', require __DIR__ . '/../config/users.php'));

$app->match('/login', function (Application $app, Request $req) {
    $security = $app['security'];

    if (null !== $security->getUserName()) {
        return $app->redirect($app['url_generator']->generate('administration_hub'));
    }

    if ($req->getMethod() === 'POST') {
         if ($security->bindSession()->getUserName() || $security->bindRequest($req)->getUsername()) {
             return $app->redirect($app['url_generator']->generate('administration_hub'));
         } else {
            $error = "L'identification a échouée.";
        }
    }

    return new TransientResponse($app['twig'], 'security/login.html.twig', array('error' => isset($error) ? $error : null));
})
->bind('login');

$app->get('/logout', function (Application $app, Request $req) {
    $app['security']->unbind();
    return $app->redirect($app['url_generator']->generate('index'));
})
->bind('logout');
