<?php

namespace phpRouter;

use Opis\JsonSchema\Schema;
use Opis\JsonSchema\Validator as SchemaValidator;
use JsonException;

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
     * @var string|null
     */
    private ?string $error;
    /**
     * @var array|null
     */
    protected ?array $result;

    /**
     * Schema constructor.
     * @param string $filename
     * @param string $base
     * @throws ValidationException
     */
    public function __construct(string $filename = "", string $base = "") {
        if($base === "") {
            if(defined("JSON_SCHEMA_BASE_DIR")) {
                $base = JSON_SCHEMA_BASE_DIR;
            }
        }
        $file = "$base$filename";
        if(!file_exists($file)) throw new ValidationException("File does not exist");
        if(!is_readable($file)) throw new ValidationException("Cannot read file");
        $content = file_get_contents($file);
        if($content === false) {
            throw new ValidationException("Internal Server Error", "Could not read schema file contents");
        }

        $this->schema = Schema::fromJsonString(file_get_contents($file));
        $this->error = null;
        $this->result = null;
    }

    /**
     * @param Request|string $request
     * @param bool $throw_on_error
     * @return $this
     * @throws ValidationException
     */
    public function validate(Request | string $request, bool $throw_on_error = true) : self {
        $body = ($request instanceof Request) ? $request->get_body() : $request;
        try {
            $payload = json_decode($body, false, 512, JSON_THROW_ON_ERROR);

            $validator = new SchemaValidator();
            $result = $validator->schemaValidation($payload, $this->schema);

            if($result->isValid()) {
                $this->result = json_decode($body, true, 512, JSON_THROW_ON_ERROR);
                return $this;
            }

            $this->error = $result->getFirstError()->keyword() . ":" . implode(",", $result->getFirstError()->keywordArgs());
            if($throw_on_error) throw new ValidationException("Validation Error", $this->error);

            return $this;
        } catch (JsonException $e) {
            throw new ValidationException("Invalid json", $e->getMessage());
        }
    }

    public function cast() : mixed {}

    /**
     * @return bool
     */
    public function has_error() : bool {
        return !is_null($this->error);
    }

    /**
     * @return string
     */
    public function get_error() : string {
        if(!$this->has_error()) return "";
        return $this->error;
    }

    /**
     * @return array
     */
    public function get_result() : array {
        return $this->result;
    }

}