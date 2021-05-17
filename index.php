<?php

require_once 'vendor/autoload.php';
require_once __DIR__ . "/CheckJson.php";

use phpRouter\Router;
use phpRouter\Request;
use phpRouter\Response;
use phpRouter\View;
use phpRouter\CheckJson;

$router = new Router();

$router->uses(new CheckJson());

$router->serve("/dist/(.*)", __DIR__ . "/dist/");

$router->get("/", function(Request $req, Response $res) {
    $res->send("Hello Amigo");
});

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