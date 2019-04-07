<?php
/**
 * Created by PhpStorm.
 * User: Marco
 * Date: 07.04.2019
 * Time: 17:58
 */

namespace PHPRouting\routing\handler;

use PHPRouting\routing\Request;
use PHPRouting\routing\response\Response;

interface Handler
{

    public function run(Request $req, Response $res) : void;

}