<?php

require_once 'vendor/autoload.php';

use phpRouter\Router;
use phpRouter\Request;
use phpRouter\Response;
use phpRouter\View;
use phpRouter\Middleware;
use phpRouter\NextFunction;

$start = microtime(true);

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

$router->serve("/dist/(.*)", __DIR__ . "/dist/");

$router->get("/", function(Request $req, Response $res) use($start) {
    $diff = round(microtime(true) - $start, 3) * 1000;
    $res->send("Required $diff ms");
});

$router->get("/login", function(Request $req, Response $res) {
    $res->send("ok");
}, [new TestMiddleware()]);

$router->not_found(function(Request $req, Response $res) {
    $res->send_error("Not found");
});

$router->on_error(function(Request $req, Response $res, string $error) {
    if($req->get_content_type() !== "application/json") {
        $res->render(new View(function() use ($error) {
            ?>
            <h1>Error</h1>
            <p><?= $error ?></p>
            <?php
        }));
    }
});

$router->run();