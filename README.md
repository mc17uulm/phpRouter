# phpRouter

Version 3.4.0

### Usage

#### Basic Example

**index.php:**

```php

require_once 'vendor/autoload.php';

use phpRouter\Router;
use phpRouter\Request;
use phpRouter\Response;
use phpRouter\Middleware;
use phpRouter\NextFunction;

final class GlobalMiddleware implements Middleware {
    public function __invoke(Request $request, Response $response, NextFunction $next): void
    {
        $response->add_header('X-Modified-Header', 'true');
        $next();
    }
}

final class TestMiddleware implements Middleware {
    public function __invoke(Request $request, Response $response, NextFunction $next): void
    {
        $response->set_content_type('application/json');
        $next();
    }
}


$router = new Router();

$router->uses(GlobalMiddleware::class);

$router->serve("/files/(.*)", __DIR__ . '/../files/');

$router->use_namespace([
    // classes extending IRouter
]);

$router->get("/", function(Request $req, Response $res) {
    $res->show(new Index());
});

$router->get('/test', [Test::class, 'handle']);

$router->get("/error", function(Request $req, Response $res) {
    throw new Exception("internal server error");
});

$router->get("/param/(?P<token>[a-zA-Z0-9-]+)", function(Request $req, Response $res) {
    $token = $req->get_match('token');
    $res->send($token);
});

$router->get("/params/(?P<id>\d+)/(?P<token>[a-zA-Z0-9-]+)", function(Request $req, Response $res) {
    $token = $req->get_match('token');
    $id = $req->get_match('id');
    $res->send("$token: $id");
});

$router->get("/login", function(Request $req, Response $res) {
    $res->send("ok");
}, [TestMiddleware::class]);

$router->not_found(function(Request $req, Response $res) {
    $res->send_error("Not found");
});

$router->on_error(function(Request $req, Response $res, string $error) {
    $res->send($error);
});

$router->run();

```

More examples in ``index.php``

### Changelog

**v3.4.0**
* added ``group()`` to ``Router`` for namespacing
* removed own rendering library ``View``
* added generic ``Validator`` for json schemas on ``Request``
* ``get_path()`` on  ``Request`` returns now path (former function of ``get_url()``); ``get_url()`` now returns full url
* all ``never`` functions on ``Response`` can now be added with a http code. Removed ``code`` property from class
* added alias ``view()`` for ``blade()`` to ``Response``
* fixed some bugs

**v3.3.6**
* updated header handling

**v3.3.5**
* fixed invalid phpdocs caused by v3.3.4

**v3.3.4**

* enabled usage dynamic middlewares

**v3.3.3**

* execute callbacks via ``[ClassName::class, 'function']``

**v3.3.2**

* JsonSchema::validate() first argument can now be Request or string

**v3.3.1**

* added more tests
* middleware is now added as class string not as an object
* fixed bug in header allocation

**v3.3**

* updated docker image to php8.0
* added phpunit and tests for basic routes
* added WordPress REST API flavoured parameter regex
* added new preg_match() pattern
* added phpstan for static analyse

**v3.2**

* requires now min php8.0
* error returns now key 'debug' not 'debug_message'
* request object can now compare content-type values

**v3.1.5**

* added support for blade template engine

**v3.1.4**

* JsonSchema can now return result
* updated example

**v3.1.3**

* each route can now have their own middlewares
* middleware handling now in Route class

**v3.1.2**

* added ip address to request

**v3.1.1**

* send error returns 400 http code

**v3.1.0**

* added Middleware interface for typeable functions

**v3.0.6**

* added NextFunction wrapper
* renamed 'requires' middleware function to 'uses' ('requires' can still be used)

**v3.0.5**

* added middleware handler