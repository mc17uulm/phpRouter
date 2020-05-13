<?php

namespace phpRouter;

use MyCLabs\Enum\Enum;

/**
 * Class HTTPRequestType
 *
 * @author mc17
 * @version 2.0.0
 * @since 2.0.0
 *
 * @package phpRouter
 *
 * @method static self GET()
 * @method static self POST()
 * @method static self PUT()
 * @method static self DELETE()
 * @method static self HEAD()
 * @method static self OPTIONS()
 */
final class HTTPRequestType extends Enum
{

    private const GET = "GET";
    private const POST = "POST";
    private const PUT = "PUT";
    private const DELETE = "DELETE";
    private const HEAD = "HEAD";
    private const OPTIONS = "OPTIONS";

}