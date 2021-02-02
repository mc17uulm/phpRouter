# phpRouter

Version 3.0.3

### Usage

#### Basic Example

**index.php:**

```php

require_once 'vendor/autoload.php';

use phpRouter\Router;
use phpRouter\Request;
use phpRouter\Response;

$router = new Router();

$router->serve("/files/(.*)", __DIR__ . '/../files/');

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