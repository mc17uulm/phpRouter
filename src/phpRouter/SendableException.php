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
    private string $public_message;

    /**
     * SendableException constructor.
     * @param string $debug_message
     * @param string $public_message
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct(string $debug_message = "", string $public_message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($debug_message, $code, $previous);
        $this->public_message = $public_message;
    }

    /**
     * @return string
     */
    public function get_public_message() : string {
        return $this->public_message;
    }

}