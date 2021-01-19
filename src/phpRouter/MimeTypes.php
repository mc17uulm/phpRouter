<?php

namespace phpRouter;

final class MimeTypes
{

    /**
     * @param string $type
     * @return string
     */
    public static function find_type(string $type) : string {
        $types = self::get_types();
        if(isset($types[$type])) {
            return $types[$type];
        }
        return "text/plain";
    }

    public static function get_types() : array {
        return require_once __DIR__ . "/../../mimes.php";
    }

}