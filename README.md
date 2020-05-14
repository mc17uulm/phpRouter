# phpRouter

Basic phpRouter for Apache Server

### Usage

#### Basic Example

**index.php:**

```php

require_once 'vendor/autoload.php';

use phpRouter\Router;
use phpRouter\Request;
use phpRouter\Response;

$router = new Router("/api");

$router->get("/", function(Request $req, Response $res) {
    $res->send_success("running");
});

$router->not_found(function(Request $req, Response $res) {
    $res->send_error("Not found");
});

$router->run();

```