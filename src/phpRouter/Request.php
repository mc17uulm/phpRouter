<?php

namespace phpRouter;

use JsonException;

/**
 * Class Request
 * @package phpRouter
 */
final class Request
{

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
     * @param string $url
     * @param string $type
     * @param array $params
     * @param string $content_type
     * @param array $headers
     * @param string $body
     */
    public function __construct(string $url, string $type, array $params, string $content_type, array $headers, string $body)
    {
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
     * @return array
     * @throws RouterException
     */
    public function get_json() : array {
        try {
            return json_decode($this->body, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw new RouterException("JsonException: {$e->getMessage()}");
        }
    }

}