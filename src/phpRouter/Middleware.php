<?php

namespace phpRouter;

/**
 * Interface Middleware
 * @package phpRouter
 */
interface Middleware
{

    /**
     * @param Request $request
     * @param Response $response
     * @param NextFunction $next
     */
    public function __invoke(Request $request, Response $response, NextFunction $next) : void;

}