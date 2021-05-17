<?php

namespace phpRouter;

use JsonException;
use stdClass;

/**
 * Class Request
 * @package phpRouter
 */
final class Request
{

    /**
     * @var string
     */
    private string $ip;
    /**
     * @var string
     */
    private string $url;
    /**
     * @var string
     */
    private string $type;
    /**
     * @var array
     */
    private array $params;
    /**
     * @var string
     */
    private string $content_type;
    /**
     * @var array
     */
    private array $headers;
    /**
     * @var array
     */
    private array $matches;
    /**
     * @var string
     */
    private string $body;

    /**
     * Request constructor.
     * @param string $ip
     * @param string $url
     * @param string $type
     * @param array $params
     * @param string $content_type
     * @param array $headers
     * @param string $body
     */
    public function __construct(string $ip, string $url, string $type, array $params, string $content_type, array $headers, string $body)
    {
        $this->ip = $ip;
        $this->url = $url;
        $this->type = $type;
        $this->params = $params;
        $this->content_type = $content_type;
        $this->headers = $headers;
        $this->matches = [];
        $this->body = $body;
    }

    /**
     * @return string
     */
    public function get_ip() : string {
        return $this->ip;
    }

    /**
     * @return string
     */
    public function get_url() : string {
        return $this->url;
    }

    /**
     * @return string
     */
    public function get_type() : string {
        return $this->type;
    }

    /**
     * @return array
     */
    public function get_params() : array {
        return $this->params;
    }

    /**
     * @param string $key
     * @return mixed
     * @throws RouterException
     */
    public function get_param(string $key) {
        if(!array_key_exists($key, $this->params)) throw new RouterException("Key '$key' not set in request parameters");
        return $this->params[$key];
    }

    /**
     * @return string
     */
    public function get_content_type() : string {
        return $this->content_type;
    }

    /**
     * @return array
     */
    public function get_headers() : array {
        return $this->headers;
    }

    /**
     * @return array
     */
    public function get_matches() : array {
        return $this->matches;
    }

    /**
     * @param array $matches
     */
    public function set_matches(array $matches) : void {
        $this->matches = $matches;
    }

    /**
     * @return string
     */
    public function get_body() : string {
        return $this->body;
    }

    /**
     * @param bool $assoc
     * @return array|stdClass
     * @throws RouterException
     */
    public function get_json(bool $assoc = true) {
        try {
            return json_decode($this->body, $assoc, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw new RouterException("JsonException: {$e->getMessage()}");
        }
    }

    /**
     * @return int
     * @throws RouterException
     */
    public function get_path_id() : int {
        if(count($this->matches) !== 1) throw new RouterException("Count of matches for path id invalid");
        $id = $this->matches[0];
        if(!is_numeric($id)) throw new RouterException("Id not numeric");
        return $id;
    }

    /**
     * @param JsonSchema $schema
     * @return array
     * @throws RouterException
     * @throws ValidationException
     */
    public function get_json_payload(JsonSchema $schema) : array {
        $schema->validate($this->get_json(false));
        return $this->get_json();
    }

}