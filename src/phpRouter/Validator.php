<?php

namespace phpRouter;

final class Validator
{

    /**
     * @param array $payload
     * @param array $check
     * @param bool $throw_on_error
     * @return bool
     * @throws SendableException
     */
    public static function valid_payload(array $payload, array $check, bool $throw_on_error = true) : bool {
        $result = array_reduce(
            array_map(function(string $key) use ($check, $payload) {
                if(array_key_exists($key, $payload)) {
                    return self::check_type($check[$key], $payload[$key]);
                } else {
                    return gettype($check[$key]) === "string" ? (substr($check[$key], 0, 1) === "?") : false;
                }
            }, array_keys($check)),
            function($carry, bool $val) {
                return $carry && $val;
            },
            true
        );
        if($throw_on_error && !$result) {
            throw new SendableException("Validation error");
        }
        return $result;
    }

    /**
     * @param mixed $type
     * @param mixed $data
     * @return bool
     * @throws SendableException
     */
    public static function check_type($type, $data) : bool {
        switch(gettype($type)) {
            case "string":
                if(substr($type, 0, 1) === "?") {
                    $type = substr($type, 1);
                }
                return gettype($data) === $type;
            case "array": return self::valid_payload($data, $type);
            default: return false;
        }
    }

}