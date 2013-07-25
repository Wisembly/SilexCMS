<?php

use Silex\Application;

use Symfony\Component\HttpFoundation\Request;
use SilexCMS\Response\TransientResponse;

$app->match('/login', function (Application $app, Request $req) {
    $security = $app['silexcms.security'];

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

    return new TransientResponse($app, $app['silexcms.template.loader']->load('security/login.html.twig'), array('error' => isset($error) ? $error : null));
})
->bind('administration_login');

$app->get('/logout', function (Application $app, Request $req) {
    $app['silexcms.security']->unbind();
    return $app->redirect($app['url_generator']->generate('index'));
})
->bind('logout');
