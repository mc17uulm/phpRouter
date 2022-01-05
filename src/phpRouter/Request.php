<?php

namespace phpRouter;

/**
 * Class Request
 * @package phpRouter
 */
final class Request
{

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
     * @param string $path
     * @param string $type
     * @param array $params
     * @param string $content_type
     * @param array $headers
     * @param string $body
     */
    public function __construct(
        private string $ip,
        private string $url,
        private string $path,
        private string $type,
        private array $params,
        private string $content_type,
        array $headers,
        string $body
    )
    {
        $this->headers = $this->parse_headers($headers);
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
    public function get_path() : string {
        return $this->path;
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
     * @param mixed $default
     * @return mixed
     * @throws RouterException
     */
    public function get_param(string $key, mixed $default = null): mixed {
        if(!array_key_exists($key, $this->params)) {
            if($default === null) throw new RouterException("Key '$key' not set in request parameters");
            return $default;
        }
        return $this->params[$key];
    }

    /**
     * @return string
     */
    public function get_content_type() : string {
        return $this->content_type;
    }

    /**
     * @param string $content_type
     * @return bool
     */
    public function has_content_type(string $content_type) : bool {
        return str_contains($this->content_type, $content_type);
    }

    /**
     * @return array
     */
    public function get_headers() : array {
        return $this->headers;
    }

    /**
     * @param string $key
     * @return string|null
     */
    public function get_header(string $key) : string | null {
        if(array_key_exists(strtolower($key), $this->headers)) {
            return $this->headers[strtolower($key)];
        }
        return null;
    }

    /**
     * @param string $key
     * @return mixed
     * @throws RouterException
     */
    public function get_match(string $key) : mixed {
        if(!array_key_exists($key, $this->matches)) throw new RouterException("Key '$key' not set in request queries");
        return $this->matches[$key];
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
     * @param array $headers
     * @return array
     */
    private function parse_headers(array $headers) : array {
        $_headers = [];
        foreach($headers as $key => $value) {
            $_headers[strtolower($key)] = $value;
        }
        return $_headers;
    }

}