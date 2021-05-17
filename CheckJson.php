<?php

namespace phpRouter;

/**
 * Class CheckJson
 * @package phpRouter
 */
final class CheckJson implements Middleware
{

    /**
     * @param Request $request
     * @param Response $response
     * @param NextFunction $next
     * @throws SendableException
     */
    public function __invoke(Request $request, Response $response, NextFunction $next): void
    {
        if($request->get_content_type() === "application/json") {
            throw new SendableException("Only accept json");
        }
        $next();
    }

}