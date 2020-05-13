<?php

namespace phpRouter;

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

    private array $routes;
    private string $path;
    private HTTPRequestType $type;
    private ?string $handle_not_found = null;
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

        if(isset($url["path"])) {
            $this->path = trim(substr($url["path"], strlen($prefix)));
        }

    }

    private function add(string $expression, HTTPRequestType $type, callable $function, string $dir = null) : void
    {
        array_push($this->routes, array(
            "expression" => $expression,
            "function" => $function,
            "type" => $type,
            "dir" => $dir
        ));
    }

    public function get(string $expression, callable $function) : void
    {
        $this->add($expression, HTTPRequestType::GET(), $function);
    }

    public function post(string $expression, callable $function) : void
    {
        $this->add($expression, HTTPRequestType::POST(), $function);
    }

    public function head(string $expression, callable $function) : void
    {
        $this->add($expression, HTTPRequestType::HEAD(), $function);
    }

    public function not_found(callable $function) : void
    {
        $this->handle_not_found = $function;
    }

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

            if((preg_match("#$expression#", $this->path, $matches)) && (in_array($this->type, $route["type"])))
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