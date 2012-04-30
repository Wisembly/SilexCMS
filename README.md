# SilexCMS

## About

This project aims to provide a minimal toolset helping to create corporate websites. Using [Silex](http://silex.sensiolabs.org/) as primary framework, it includes multiple shorthand classes.

## Documentation

### Pages

There is two kind of web pages : statics and dynamics.

Static pages does not rely on anything else than their templates. Dynamic ones take parameters in their urls, fetch a table, then render the specified template, storing the resulting objects in an accessible variable.

#### Static

```php
$app->register(new SilexCMS\Page\StaticPage('/', 'home.html.twig'));
```

#### Dynamic

```php
$app->register(new SilexCMS\Page\DynamicPage('/product/{slug}', 'product.html.twig'));
```

```
{% if app.set | size > 0 %}
    Our product is called {{ app.set[0].name }} :)
{% else %}
    Product not found :(
{% endif %}
```

### Security

The security classes give a very simple way to identifying some users.

When instanciating a Firewall, you will only have to provide a name and an array containing your users authentification infos (where the key will be their usernames and values are plain text passwords). A logger instance will be automagically created in the `app[name]` variable.

From then, you can use this logger to check current user state or change it.

#### Manual example

```php
$app->register(new SilexCMS\Security\Firewall('main', array('user' => 'pass')));

var_dump($app['main']->getUsername()); // null
$app['main']->bindUsername('user');
var_dump($app['main']->getUsername()); // "user"
```

#### Request example

You can also bind requests if they have at least two parameters : `_username` and `_password`.

##### startup.php
```php
$app->register(new SilexCMS\Security\Firewall('security', array('user' => 'pass')));

$app->register(new SilexCMS\Page\StaticPage('/login', 'login.html.twig'));
$app->register(new SilexCMS\Page\StaticPage('/login/success', 'login/success.html.twig'));
$app->register(new SilexCMS\Page\StaticPage('/login/failure', 'login/failure.html.twig'));

$app->post('/post', function (Application $app, Request $req) {
    $security = $app['security'];

    if ($security->bindSession()->getUserName() || $security->bindRequest($req)->getUserName()) {
        return $app->redirect('login/success');
    } else {
        return $app->redirect('login/failure');
    }
});
```

##### login.html.twig
```
<form action="/login" method="post">
    <input type="text" name="_password" /><br />
    <input type="password" name="_password" /><br />
    <input type="submit" />
</form>
```

## License

SilexCMS is licensed under the MIT license.
