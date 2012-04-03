DynamicPage
===========

The *DynamicPage* is a convenient way to display a set of user-requested informations.

It takes the arguments of the route providen in the url, then load matching entries from the database. These entries are finally renderer by a template.

The query result will be store in $app['set'].

Currently, if there is no result, no specific action will be made.

Usage
-----

    $app->register(new DynamicPage('/comments/{id_post}', 'comments', 'comments.html.twig'));

or

    $app->register(new DynamicPage('/comments/{id_post}', 'comments'));

And the template will be something like (assuming the query) :

    {% for comment in app.set %}
        <fieldset>
            <legend>{{ comment.user }}</legend>
            {{ comment.content }}
        </fieldset>
    {% endfor %}
