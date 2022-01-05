<?php

namespace phpRouter;

/**
 * Class Validator
 * @package phpRouter
 */
final class Validator
{

    /**
     * @param Request $req
     * @param string $schema
     * @return array
     * @throws ValidationException
     */
    public static function parse(Request $req, string $schema) : array {
        if(!str_contains($schema, '.schema.json')) {
            $schema = "$schema.schema.json";
        }
        $schema = new JsonSchema($schema);
        $schema->validate($req);
        return $schema->get_result();
    }

}