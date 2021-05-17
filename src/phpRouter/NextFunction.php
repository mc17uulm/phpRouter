<?php

namespace phpRouter;

use Closure;

/**
 * Class NextFunction
 * @package phpRouter
 */
final class NextFunction
{

    /**
     * @var Request
     */
    private Request $request;
    /**
     * @var Response
     */
    private Response $response;
    /**
     * @var array<Middleware | Closure>
     */
    private array $middlewares;

    /**
     * NextFunction constructor.
     * @param array<Middleware | Closure> $middlewares
     * @param Request $request
     * @param Response $response
     */
    public function __construct(array $middlewares, Request $request, Response $response)
    {
        $this->middlewares = $middlewares;
        $this->request  = $request;
        $this->response = $response;
    }

    /**
     * Call middleware function with next function
     */
    public function __invoke() : void {
        $_middlewares = $this->middlewares;
        if(count($_middlewares) === 0) return;
        $callable = array_shift($_middlewares);
        if(count($this->middlewares) === 1) {
            $callable($this->request, $this->response);
        } else {
            $callable($this->request, $this->response, new NextFunction($_middlewares, $this->request, $this->response));
        }
    }

}