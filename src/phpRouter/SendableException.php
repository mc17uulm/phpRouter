<?php

namespace phpRouter;

use Exception;
use Throwable;

/**
 * Class SendableException
 * @package phpRouter
 */
class SendableException extends Exception {

    /**
     * @var string
     */
    private string $debug_message;

    /**
     * SendableException constructor.
     * @param string $message
     * @param string $debug_message
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct(string $message = "", string $debug_message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->debug_message = $debug_message;
    }

    /**
     * @return string
     */
    public function get_debug_message() : string {
        return $this->debug_message;
    }

}