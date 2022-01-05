<?php

namespace phpRouter;

/**
 * Class Route
 * @package phpRouter
 */
final class Route {

    private string $query;
    /**
     * @var callable
     */
    private $func;

    /**
     * Route constructor.
     * @param string $type
     * @param string $query
     * @param callable $func
     * @param array<string|Middleware> $middlewares
     * @param string|null $namespace
     */
    public function __construct(
        private string $type,
        string $query,
        callable $func,
        private array $middlewares = [],
        ?string $namespace = null
    ) {
        $this->query = $namespace === null ? $query : "$namespace$query";
        $this->func = $func;
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
     * @param array<string|Middleware> $middlewares
     * @throws RouterException
     */
    public function execute(Request $request, Response $response, array $middlewares = []) : void {
        $_function = $this->func;
        $_middlewares = array_map(
            function(string | Middleware $class_name) : Middleware {
                if($class_name instanceof Middleware) return $class_name;
                $implemented = class_implements($class_name);
                if(!$implemented) throw new RouterException('given middleware does not implement Middleware interface');
                if(!class_exists($class_name) || !in_array(Middleware::class, $implemented)) {
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