<?php

require_once 'vendor/autoload.php';

use phpRouter\Router;
use phpRouter\Request;
use phpRouter\Response;
use phpRouter\View;
use phpRouter\Middleware;
use phpRouter\NextFunction;

$start = microtime(true);

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

$router->serve("/dist/(.*)", __DIR__ . "/dist/");

$router->get("/", function(Request $req, Response $res) {
    $res->send("ok");
});

final class Test {
    public static function handle(Request $req, Response $res) {
        $res->send('ok callable');
    }
}

$router->get('/callable_router', [Test::class, 'handle']);

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
    $res->send_error("not found", "", 404);
});

$router->on_error(function(Request $req, Response $res, string $error) {
    if($req->get_content_type() !== "application/json") {
        $res->render(new View(function() use ($error) {
            ?>
            <h1>Error</h1>
            <p><?= $error ?></p>
            <?php
        }), 400);
    }
});

$router->run();