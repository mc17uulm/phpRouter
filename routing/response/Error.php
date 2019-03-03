<?php
/**
 * Created by PhpStorm.
 * User: mc17uulm
 * Date: 03.03.2019
 * Time: 14:27
 */

namespace PHPRouting\routing\response;

class Error
{

    private $msg;

    public function __construct($msg = "")
    {
        $this->msg = $msg;
    }

    public function __toString() : string
    {
        return json_encode(array("type" => "error", "msg" => $this->msg));
    }

}