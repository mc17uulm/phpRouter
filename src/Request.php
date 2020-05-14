<?php

namespace phpRouter;

use JsonException;

final class Request
{

    private string $uri;
    private HTTPRequestType $type;
    private string $content_type;
    private array $parameters;
    private array $matches;
    private array $headers;
    private string $body;
    private array $payload;
    private array $files;

    /**
     * Request constructor.
     * @param string $uri
     * @param HTTPRequestType $type
     * @throws RouterException
     */
    public function __construct(string $uri, HTTPRequestType $type)
    {
        $this->uri = $uri;
        $this->type = $type;
        $this->content_type = $_SERVER["CONTENT_TYPE"] ?? "text/plain";
        $this->parameters = $this->load_parameters();
        $this->headers = apache_request_headers();
        $this->body = file_get_contents("php://input");
        $this->payload = [];
        if($this->content_type === "application/json"){
            try {
                $this->payload = json_decode($this->body, true, 512, JSON_THROW_ON_ERROR);
            } catch (JsonException $e) {
                throw new RouterException($e->getMessage());
            }
        }
        $this->files = $_FILES ?? [];
    }

    private function load_parameters() : array
    {
        $parameters = [];
        if(isset($_GET)) {
            foreach($_GET as $key => $value) {
                $parameters[$key] = $value;
            }
        }
        return $parameters;
    }

    public function get_payload() : array
    {
        return $this->payload;
    }

    public function is_post_request() : bool
    {
        return $this->type->equals(HTTPRequestType::POST());
    }

    public function has_valid_csrf_token(string $token) : bool
    {
        if(isset($this->headers["csrf_token"])) {
            return $this->headers["csrf_token"] === $token;
        }
        return false;
    }

}