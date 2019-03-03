<?php
/**
 * Created by PhpStorm.
 * User: mc17uulm
 * Date: 03.03.2019
 * Time: 14:29
 */

namespace PHPRouting\routing\response;

class Success
{

    private $msg;

    public function __construct($msg = "")
    {
        $this->msg = $msg;
    }

    public function __toString() : string
    {
        return json_encode(array("type" => "success", "msg" => $this->msg));
    }

}