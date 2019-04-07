<?php
/**
 * Created by PhpStorm.
 * User: mc17uulm
 * Date: 03.03.2019
 * Time: 14:08
 */

namespace PHPRouting\routing;

use PHPRouting\routing\handler\APIHandler;
use PHPRouting\routing\response\Response;

class Loader
{

    public static function handle() : void
    {

        Router::init();

        Router::add_dir("/public", __DIR__ . "/../secret");

        Router::add_handler("/api", new APIHandler());

        Router::get("/", function(Request $req, Response $res) {
            $res->send_success();
        });

        Router::get("/index", function(Request $req, Response $res) {
           $res->send_file(__DIR__ . "/../index.html");
        });

        Router::handle404(function() {
           echo "404";
        });

        Router::run();

    }

}