<?php

namespace phpRouter;

/**
 * Interface IRouter
 * @package phpRouter
 */
interface IRouter {

    /**
     * @param Router $router
     */
    public static function run(Router $router) : void;

}