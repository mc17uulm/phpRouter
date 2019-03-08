<?php
/**
 * Created by PhpStorm.
 * User: mc17uulm
 * Date: 03.03.2019
 * Time: 14:12
 */

namespace PHPRouting\routing;

use PHPRouting\routing\response\Response;

class Router
{

    private static $routes = array();
    private static $path;
    private static $type;
    private static $code404;
    private static $response_type;

    public static function init(string $prefix = "", string $response_type = "text/html; charset=utf-8") : void
    {

        self::$response_type = $response_type;
        $parsed_url = parse_url($_SERVER["REQUEST_URI"]);

        switch($_SERVER["REQUEST_METHOD"])
        {
            case "GET": self::$type = RoutingType::GET;
                break;
            case "POST": self::$type = RoutingType::POST;
                break;
            case "HEAD": self::$type = RoutingType::HEAD;
                break;
            case "PUT": self::$type = RoutingType::PUT;
                break;
            case "DELETE": self::$type = RoutingType::DELETE;
                break;
            default: self::$type = RoutingType::RESTRICTED;
                break;
        }

        if(isset($parsed_url["path"]))
        {
            self::$path = trim(substr($parsed_url["path"], strlen($prefix)));
        }
        else
        {
            self::$path = "";
        }

    }

    private static function add(string $expression, string $type, callable $function) : void
    {
        array_push(self::$routes, array(
           "expression" => $expression,
           "function" => $function,
           "type" => $type
        ));
    }

    public static function get(string $expression, callable $function) : void
    {
        self::add($expression, RoutingType::GET, $function);
    }

    public static function post(string $expression, callable $function) : void
    {
        self::add($expression, RoutingType::POST, $function);
    }

    public static function head(string $expression, callable $function) : void
    {
        self::add($expression, RoutingType::HEAD, $function);
    }

    public static function handle404(callable $function) : void
    {
        self::$code404 = $function;
    }

    public static function run(string $basepath = "") : void
    {
        $route_found = false;

        foreach(self::$routes as $route)
        {
            if($basepath!=="")
            {
                $route["expression"] = "($basepath)/" . $route["expression"];
            }

            $route["expression"] = "^" . $route["expression"] . "$";
            $route["expression"] = "^" . $route["expression"] . "$";

            if((preg_match("#".$route["expression"]."#", self::$path, $matches)) && ($route["type"] === self::$type))
            {
                array_shift($matches);
                if($basepath!=="")
                {
                    array_shift($matches);
                }

                $request = new Request($route["type"], $route["expression"]);
                $request->add_matches($matches);
                $response = new Response();
                $response->set_content_type(self::$response_type);

                $route_found = true;
                call_user_func_array($route["function"], array($request, $response));

            }

        }

        if(!$route_found)
        {
            call_user_func_array(self::$code404, array(self::$path));
        }
    }

}