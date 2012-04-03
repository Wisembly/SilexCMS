StaticPage
==========

The *StaticPage* is a module providing a link between a route and a template.

    $app->register(new StaticPage($route, $template));

It's a one-liner for something like this :

    $app->get($route, function (Application $app) use ($template) {
        return new TransientResponse($app['twig'], $template);
    });

As you can see, it only works with the GET method. If you want to use another method, you will have to use your own controller (these methods usually involves some logic behind the hood).
