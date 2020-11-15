<?php

namespace phpRouter;

use Closure;

/**
 * Class Router
 * @package phpRouter
 */
final class Router
{

    /**
     * @var array
     */
    private array $routes;
    /**
     * @var Request
     */
    private Request $request;
    /**
     * @var string
     */
    private string $path;
    /**
     * @var Closure|null
     */
    private ?Closure $not_found = null;
    /**
     * @var bool
     */
    private bool $debug;

    /**
     * Router constructor.
     * @param bool $debug
     */
    public function __construct(bool $debug = false) {

        $this->routes = [];

        $url = parse_url($_SERVER["REQUEST_URI"]);
        $this->path = $url["path"];

        $this->request = new Request(
            $this->path,
            $_SERVER["REQUEST_METHOD"],
            $this->load_parameters(),
            $_SERVER["CONTENT_TYPE"] ?? "text/plain",
            apache_request_headers(),
            file_get_contents("php://input")
        );

        $this->debug = $debug;

    }

    /**
     * @return array
     */
    private function load_parameters() : array
    {
        $parameters = [];
        if(isset($_GET)) {
            foreach($_GET as $key => $value) {
                $parameters[$key] = $value;
            }
        }
        return $parameters;
    }

    /**
     * @param string $query
     * @param callable $func
     */
    public function get(string $query, callable $func) : void {
        array_push(
            $this->routes,
            new Route(
                "GET",
                $query,
                $func
            )
        );
    }

    /**
     * @param string $query
     * @param callable $func
     */
    public function post(string $query, callable $func) : void {
        array_push(
            $this->routes,
            new Route(
                "POST",
                $query,
                $func
            )
        );
    }

    /**
     * @param string $query
     * @param callable $func
     */
    public function put(string $query, callable $func) : void {
        array_push(
            $this->routes,
            new Route(
                "PUT",
                $query,
                $func
            )
        );
    }

    /**
     * @param string $query
     * @param callable $func
     */
    public function delete(string $query, callable $func) : void {
        array_push(
            $this->routes,
            new Route(
                "DELETE",
                $query,
                $func
            )
        );
    }

    /**
     * @param string $query
     * @param string $dir
     */
    public function serve(string $query, string $dir) : void {
        array_push(
            $this->routes,
            new Route(
                "GET",
                $query,
                function(Request $req, Response $res) use ($dir) {
                    Router::handle_dir_request($req, $res, $dir);
                }
            )
        );
    }

    /**
     * @param Request $req
     * @param Response $res
     * @param string $dir
     */
    private static function handle_dir_request(Request $req, Response $res, string $dir) : void {
        $path = $req->get_matches()[0];
        $base = realpath($dir);
        $filepath = realpath("$dir$path");
        if(strpos($filepath, $base) !== 0) {
            $res->set_http_code(404);
            $res->send("Invalid path");
        }
        if(!is_file($filepath)) {
            $res->set_http_code(404);
            $res->send("File not found");
        }
        if(!is_readable($filepath)) {
            $res->set_http_code(404);
            $res->send("File not readable");
        }
        $res->set_http_code(200);
        $res->set_content_type(mime_content_type($filepath));
        $res->send(file_get_contents($filepath));
    }

    /**
     * @param callable $func
     */
    public function not_found(callable $func) : void {
        $this->not_found = $func;
    }

    public function run() : void {
        $route_found = false;
        $response = new Response($this->debug);

        foreach($this->routes as $route) {
            assert($route instanceof Route);
            $expression = $route->get_query();
            $expression = "^$expression$";

            if((preg_match("#$expression#", $this->path, $matches)) && $this->request->get_type() === $route->get_type()) {
                array_shift($matches);
                $route_found = true;
                $this->request->set_matches($matches);
                call_user_func_array($route->get_function(), [$this->request, $response]);
            }
        }

        if(!$route_found) {
            $response->set_http_code(404);
            call_user_func_array($this->not_found, [$this->request, $response]);
        }
    }

}