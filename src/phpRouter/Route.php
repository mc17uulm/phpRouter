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
     * Route constructor.
     * @param string $type
     * @param string $query
     * @param Closure $func
     */
    public function __construct(string $type, string $query, Closure $func)
    {
        $this->type = $type;
        $this->query = $query;
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
     * @return callable
     */
    public function get_function() : callable {
        return $this->func;
    }

}