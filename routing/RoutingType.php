<?php
/**
 * Created by PhpStorm.
 * User: mc17uulm
 * Date: 03.03.2019
 * Time: 14:14
 */

namespace PHPRouting\routing;

abstract class RoutingType
{

    const GET = "GET";
    const POST = "POST";
    const HEAD = "HEAD";
    const PUT = "PUT";
    const DELETE = "DELETE";
    const OPTIONS = "OPTIONS";
    const RESTRICTED = null;


}