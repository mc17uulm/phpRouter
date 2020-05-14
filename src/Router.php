<?php

namespace phpRouter;

use Closure;

/**
 * Class Router
 *
 * @author mc17
 * @version 2.0.0
 *
 * @package phpRouter
 */
final class Router
{

    /**
     * @var array
     */
    private array $routes;
    /**
     * @var string
     */
    private string $path;
    /**
     * @var HTTPRequestType
     */
    private HTTPRequestType $type;
    /**
     * @var Closure|null
     */
    private ?Closure $handle_not_found = null;
    /**
     * @var string
     */
    private string $response_type;

    /**
     * Router constructor.
     * @param string $prefix
     */
    public function __construct(string $prefix = "")
    {
        $this->type = new HTTPRequestType($_SERVER["REQUEST_METHOD"]);
        $this->response_type = $this->type->equals(HTTPRequestType::POST()) ? "application/json" : "text/html";

        $url = parse_url($_SERVER["REQUEST_URI"]);
        $this->path = "";
        $this->routes = [];

        if(isset($url["path"])) {
            $this->path = trim(substr($url["path"], strlen($prefix)));
        }

    }

    /**
     * @param string $expression
     * @param HTTPRequestType $type
     * @param callable $function
     * @param string|null $dir
     */
    private function add(string $expression, HTTPRequestType $type, callable $function, string $dir = null) : void
    {
        array_push($this->routes, array(
            "expression" => $expression,
            "function" => $function,
            "type" => $type,
            "dir" => $dir
        ));
    }

    /**
     * @param string $expression
     * @param callable $function
     */
    public function get(string $expression, callable $function) : void
    {
        $this->add($expression, HTTPRequestType::GET(), $function);
    }

    /**
     * @param string $expression
     * @param callable $function
     */
    public function post(string $expression, callable $function) : void
    {
        $this->add($expression, HTTPRequestType::POST(), $function);
    }

    /**
     * @param string $expression
     * @param callable $function
     */
    public function head(string $expression, callable $function) : void
    {
        $this->add($expression, HTTPRequestType::HEAD(), $function);
    }

    /**
     * @param callable $function
     */
    public function not_found(callable $function) : void
    {
        $this->handle_not_found = $function;
    }

    /**
     * @param string|null $base
     * @param bool $debug
     * @throws RouterException
     */
    public function run(string $base = null, bool $debug = false) : void
    {
        $route_found = false;

        $request = new Request($this->path, $this->type);
        $response = new Response($debug);

        foreach($this->routes as $route)
        {
            $expression = $route["expression"];
            if(!is_null($base)) {
                $expression = "($base)/$expression";
            }
            $expression = "^$expression$";

            if((preg_match("#$expression#", $this->path, $matches)) && $this->type->equals($route["type"]))
            {
                array_shift($matches);
                if(!is_null($base)) array_shift($matches);

                $route_found = true;
                $response->set_content_type($this->response_type);
                call_user_func_array($route["function"], [$request, $response]);
            }
        }

        if(!$route_found) {
            $response->set_http_code(404);
            call_user_func_array($this->handle_not_found, [$request, $response]);
        }
    }

}