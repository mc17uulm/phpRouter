# phpRouter

Version 3.1.5

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

final class TestMiddleware implements Middleware {
    public function __invoke(Request $request, Response $response, NextFunction $next): void
    {
        $response->set_content_type('application/json');
        $next();
    }
}

final class TopMiddleware implements Middleware {
    public function __invoke(Request $request, Response $response, NextFunction $next): void
    {
        $response->set_http_code(203);
        $next();
    }
}

$router = new Router();

$router->uses(new TopMiddleware());

$router->serve("/files/(.*)", __DIR__ . '/../files/');

$router->use_namespace([
    // classes extending IRouter
]);

$router->get("/", function(Request $req, Response $res) {
    $res->show(new Index());
});

$router->get("/login", function(Request $req, Response $res) {
    $res->send("ok");
}, [new TestMiddleware()]);

$router->not_found(function(Request $req, Response $res) {
    $res->send_error("Not found");
});

$router->on_error(function(Request $req, Response $res, string $error) {
    $res->send($error);
});

$router->run();

```

### Changelog

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