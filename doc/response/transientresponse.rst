TransientResponse
=================

The *TransientResponse* is a response which can be altered by the *after*-registered functions.

The template will not be rendered until the final display.

Usage
-----
    
    $resp = new TransientResponse('template.html.twig');

In order to edit the template variables, you can use *getVariables*x :

    if ($resp instanceof TransientResponse) {
        // It will set a new variable named "foo" in the template
        $resp->getVariables()->foo = 42;
    }

Another function is availabled : *getTwig*. It returns the twig instance.
