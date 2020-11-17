<?php

namespace phpRouter;

use Opis\JsonSchema\Schema;
use \Opis\JsonSchema\Validator as SchemaValidator;
use stdClass;

/**
 * Class JsonSchema
 * @package MemberDB\validator
 */
class JsonSchema {

    /**
     * @var Schema
     */
    private Schema $schema;
    /**
     * @var bool|string
     */
    private string | bool $error;

    /**
     * Schema constructor.
     * @param string $file
     * @throws SendableException
     */
    public function __construct(string $file = "") {
        if(!file_exists($file)) throw new ValidationException("File does not exist");
        if(!is_readable($file)) throw new ValidationException("Cannot read file");
        $this->schema = Schema::fromJsonString(file_get_contents($file));
        $this->error = false;
    }

    /**
     * @param stdClass $payload
     * @param bool $throw_on_error
     * @throws ValidationException
     */
    public function validate(stdClass $payload, bool $throw_on_error = true) : bool {
        $validator = new SchemaValidator();
        $result = $validator->schemaValidation($payload, $this->schema);

        if(!$result->isValid()) {
            return true;
        } else {
            $error = $result->getFirstError()->keyword() . ": " . implode(",", $result->getFirstError()->keywordArgs());
            if($throw_on_error) {
                throw new ValidationException("Validation error", $error);
            }
            $this->error = $error;
            return false;
        }
    }

    /**
     * @return string
     */
    public function get_error() : string {
        if(gettype($this->error) === "boolean") return "";
        return $this->error;
    }

}