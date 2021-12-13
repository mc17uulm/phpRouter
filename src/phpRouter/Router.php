<?php

namespace phpRouter;

use Jenssegers\Blade\Blade;
use Throwable;

/**
 * Class Router
 * @package phpRouter
 */
final class Router
{

    /**
     * @var array<Route>
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
     * @var Route|null
     */
    private ?Route $not_found = null;
    /**
     * @var Route|null
     */
    private ?Route $on_error = null;
    /**
     * @var array<string>
     */
    private array $middlewares = [];
    /**
     * @var bool
     */
    private bool $debug;
    /**
     * @var Blade|null
     */
    private ?Blade $blade = null;

    /**
     * Router constructor.
     * @param bool $debug
     * @throws RouterException
     */
    public function __construct(bool $debug = false) {

        $this->routes = [];

        $url = parse_url($_SERVER["REQUEST_URI"]);
        $this->path = $url["path"];

        $this->request = new Request(
            $this->load_ip(),
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
        foreach($_GET as $key => $value) {
            $parameters[$key] = $value;
        }
        return $parameters;
    }

    /**
     * @return string
     * @throws RouterException
     */
    private function load_ip() : string {
        if (isset($_SERVER['HTTP_CLIENT_IP']) && !empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && !empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } elseif (isset($_SERVER['REMOTE_ADDR']) && !empty($_SERVER['REMOTE_ADDR'])) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        else {
            throw new RouterException("No ip address given");
        }

        return filter_var($ip, FILTER_VALIDATE_IP);
    }

    /**
     * @param string $dir
     * @param string $cache
     */
    public function set_engine(string $dir, string $cache) : void {
        $this->blade = new Blade($dir, $cache);
    }

    /**
     * @param string $query
     * @param callable $func
     * @param array<string> $middlewares
     */
    public function get(string $query, callable $func, array $middlewares = []) : void {
        array_push(
            $this->routes,
            new Route(
                "GET",
                $query,
                $func,
                $middlewares
            )
        );
    }

    /**
     * @param string $query
     * @param callable $func
     * @param array<string> $middlewares
     */
    public function post(string $query, callable $func, array $middlewares = []) : void {
        array_push(
            $this->routes,
            new Route(
                "POST",
                $query,
                $func,
                $middlewares
            )
        );
    }

    /**
     * @param string $query
     * @param callable $func
     * @param array<string> $middlewares
     */
    public function put(string $query, callable $func, array $middlewares = []) : void {
        array_push(
            $this->routes,
            new Route(
                "PUT",
                $query,
                $func,
                $middlewares
            )
        );
    }

    /**
     * @param string $query
     * @param callable $func
     * @param array<string> $middlewares
     */
    public function delete(string $query, callable $func, array $middlewares = []) : void {
        array_push(
            $this->routes,
            new Route(
                "DELETE",
                $query,
                $func,
                $middlewares
            )
        );
    }

    /**
     * @param string $query
     * @param string $dir
     * @param array<string> $middlewares
     */
    public function serve(string $query, string $dir, array $middlewares = []) : void {
        array_push(
            $this->routes,
            new Route(
                "GET",
                $query,
                function(Request $req, Response $res) use ($dir) {
                    Router::handle_dir_request($req, $res, $dir);
                },
                $middlewares
            )
        );
    }

    /**
     * @param string $next
     */
    public function requires(string $next) : void  {
        $this->uses($next);
    }

    /**
     * @param string $next
     */
    public function uses(string $next) : void {
        array_push(
            $this->middlewares,
            $next
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
        if(!str_starts_with($filepath, $base)) {
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

        $res->set_content_type(self::get_mime_type($filepath));
        $res->send(file_get_contents($filepath));
    }

    /**
     * @param string $file
     * @return string
     */
    private static function get_mime_type(string $file) : string {
        $extension = pathinfo($file)['extension'];
        return MimeTypes::find_type($extension);
    }

    /**
     * @param callable $func
     * @param array<string> $middlewares
     */
    public function not_found(callable $func, array $middlewares = []) : void {
        $this->not_found = new Route(
            $this->request->get_type(),
            $this->path,
            $func,
            $middlewares
        );
    }

    /**
     * @param callable $func
     * @param array<string> $middlewares
     */
    public function on_error(callable $func, array $middlewares = []) : void {
        $this->on_error = new Route(
            $this->request->get_type(),
            $this->path,
            $func,
            $middlewares
        );
    }

    /**
     * @param array $provider
     * @throws RouterException
     */
    public function use_namespace(array $provider) : void {
        array_walk(
            $provider,
            function(string $class_name) {
                if(!class_exists($class_name) || !in_array(IRouter::class, class_implements($class_name))) {
                    throw new RouterException("given interface does not exist and/or does not implement IRouter interface");
                }
                $class_name::run($this);
            }
        );
    }

    public function run() : void {
        try {
            $route_found = false;
            $response = new Response($this->debug, $this->blade);

            foreach($this->routes as $route) {
                assert($route instanceof Route);
                $pattern = "@^" . $route->get_query() . "$@i";

                $match = preg_match($pattern, $this->path, $matches);
                if($match && ($this->request->get_type() === $route->get_type())) {
                    array_shift($matches);
                    $route_found = true;
                    $this->request->set_matches($matches);
                    $route->execute($this->request, $response, $this->middlewares);
                }
            }

            if(!$route_found) {
                $response->set_http_code(404);
                $this->not_found->execute($this->request, $response, $this->middlewares);
            }
        } catch (SendableException $e) {
            $this->on_error?->error(
                $this->request,
                new Response($this->debug, $this->blade),
                $e->get_public_message(),
                $e->getMessage()
            );
        } catch(Throwable $e) {
            $this->on_error?->error(
                $this->request,
                new Response($this->debug, $this->blade),
                "Internal server error",
                $e->getMessage()
            );
        }
    }

}