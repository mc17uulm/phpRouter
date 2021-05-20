<?php

namespace phpRouter;

use Closure;

/**
 * Class Route
 * @package phpRouter
 */
final class Route {

    /**
     * @var string
     */
    private string $type;
    /**
     * @var string
     */
    private string $query;
    /**
     * @var Closure
     */
    private Closure $func;
    /**
     * @var array<string>
     */
    private array $middlewares;

    /**
     * Route constructor.
     * @param string $type
     * @param string $query
     * @param Closure $func
     * @param array<string> $middlewares
     */
    public function __construct(string $type, string $query, Closure $func, array $middlewares = [])
    {
        $this->type = $type;
        $this->query = $query;
        $this->func = $func;
        $this->middlewares = $middlewares;
    }

    /**
     * @return string
     */
    public function get_type() : string {
        return $this->type;
    }

    /**
     * @return string
     */
    public function get_query() : string {
        return $this->query;
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param array<string> $middlewares
     * @throws RouterException
     */
    public function execute(Request $request, Response $response, array $middlewares = []) : void {
        $_function = $this->func;
        $_middlewares = array_map(
            function(string $class_name) : Middleware {
                if(!class_exists($class_name) || !in_array(Middleware::class, class_implements($class_name))) {
                    throw new RouterException('given middleware does not implement Middleware interface');
                }
                return new $class_name();
            },
            array_merge($middlewares, $this->middlewares)
        );
        array_push($_middlewares, function(Request $_request, Response $_response) use ($_function) {
            call_user_func_array($_function, [$_request, $_response]);
        });
        $next = new NextFunction($_middlewares, $request, $response);
        $next();
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param string $error
     * @param string $debug
     */
    public function error(Request $request, Response  $response, string $error, string $debug = "") : void {
        $function = $this->func;
        $function($request, $response, $error, $debug);
    }

}