# phpRouter

Version 3.0.5

### Usage

#### Basic Example

**index.php:**

```php

require_once 'vendor/autoload.php';

use phpRouter\Router;
use phpRouter\Request;
use phpRouter\Response;
use phpRouter\SendableException;

$router = new Router();

$router->requires(function(Request $request, Response $response, callable $next) {
    if($request->get_content_type() !== "application/json") {
        throw new SendableException("Only accept json");
    }
    $next();
});

$router->serve("/files/(.*)", __DIR__ . '/../files/');

$router->use_namespace([
    // classes extending IRouter
]);

$router->get("/", function(Request $req, Response $res) {
    $res->show(new Index());
});

$router->not_found(function(Request $req, Response $res) {
    $res->send_error("Not found");
});

$router->on_error(function(Request $req, Response $res, string $error) {
    $res->send($error);
});

$router->run();

```

### Changelog

**v3.0.5**

* added middleware handler