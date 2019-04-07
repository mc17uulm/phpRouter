<?php
/**
 * Created by PhpStorm.
 * User: mc17uulm
 * Date: 03.03.2019
 * Time: 14:25
 */

namespace PHPRouting\routing;

use PHPRouting\routing\response\Error;

class Request
{

    private $type;
    private $url;
    private $content_type;
    private $params;
    private $matches;
    private $headers;
    private $body;
    private $files;

    public function __construct(string $type, string $url)
    {

        $this->type = $type;
        $this->url = $url;
        $this->content_type = isset($_SERVER["CONTENT_TYPE"]) ? $_SERVER["CONTENT_TYPE"] : "";
        $this->params = $this->load_params();
        $this->matches = array();
        $this->headers = apache_request_headers();
        $this->body = $this->load_body();
        $this->files = $this->load_files();

    }

    public function add_matches(array $matches) : void
    {
        $this->matches = $matches;
    }

    private function load_params() : array
    {
        $params = array();
        if(isset($_GET))
        {
            foreach($_GET as $key => $value)
            {
                $params[$key] = htmlspecialchars($value);
            }
        }
        return $params;
    }
        private function load_body() : array
    {
        $body = file_get_contents('php://input');
        switch($this->content_type)
        {
            case "application/json":
                try{
                    return json_decode($body, true, 512, JSON_THROW_ON_ERROR);
                } catch(\JsonException $e) {
                    die(new Error("Invalid request"));
                }

            default:
                return array("body" => $body);
        }
    }

    private function load_files() : array
    {
        return isset($_FILES) ? $_FILES : array();
    }

    public function get_type() : string
    {
        return $this->type;
    }

    public function get_url() : string
    {
        return $this->url;
    }

    public function get_content_type() : string
    {
        return $this->content_type;
    }

    public function get_params() : array
    {
        return $this->params;
    }

    public function get_matches() : array
    {
        return $this->matches;
    }

    public function get_headers() : array
    {
        return $this->headers;
    }

    public function get_body() : array
    {
        return $this->body;
    }

    public function get_body_key($key)
    {
        return isset($this->body[$key]) ? $this->body[$key] : die(new Error("Key not set"));
    }

    public function get_files() : array
    {
        return $this->files;
    }

    public function is_valid_api_request(bool $post = false) : bool
    {
        return $this->check_http_origin() && ($post ? $this->is_post_request() : true) && $this->has_valid_csrf_token();
    }


    public function check_http_origin() : bool
    {
        if(isset($_SERVER["HTTP_REFERER"]) && isset($_SERVER["SERVER_NAME"]))
        {
            return strpos($_SERVER["HTTP_REFERER"], ($this->check_https() ? 'https://' : 'http://') . $_SERVER["SERVER_NAME"]) === 0;
        }
        return false;
    }

    public function is_post_request() : bool
    {
        if(isset($_SERVER["REQUEST_METHOD"]))
        {
            return $_SERVER["REQUEST_METHOD"] === "POST";
        }
        return false;
    }

    public function has_valid_csrf_token() : bool
    {
        if(isset($this->headers["CsrfToken"]) && isset($_SESSION["csrf_token"]))
        {
            return $this->headers["CsrfToken"] === $_SESSION["csrf_token"];
        }
        return false;
    }

    private function check_https() : bool
    {
        return ((!empty($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] !== 'off') || $_SERVER["SERVER_PORT"] == 443);
    }


}