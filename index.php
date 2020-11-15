<?php

require_once 'vendor/autoload.php';

use phpRouter\Router;
use phpRouter\Request;
use phpRouter\Response;

$router = new Router();

$router->get("/", function(Request $req, Response $res) {
    $res->send("Hello");
});

$router->not_found(function(Request $req, Response $res) {
    $res->send_error("Not found");
});

$router->run();