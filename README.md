# phpRouter

Version 3.0.6

### Usage

#### Basic Example

**index.php:**

```php

require_once 'vendor/autoload.php';

use phpRouter\Router;
use phpRouter\Request;
use phpRouter\Response;
use phpRouter\SendableException;
use phpRouter\NextFunction;

$router = new Router();

$router->uses(function(Request $request, Response $response, NextFunction $next) {
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

**v3.0.6**

* added NextFunction wrapper
* renamed 'requires' middleware function to 'uses' ('requires' can still be used)

**v3.0.5**

* added middleware handler