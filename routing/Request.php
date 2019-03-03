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
    private $body;
    private $files;

    public function __construct(string $type, string $url)
    {

        $this->type = $type;
        $this->url = $url;
        $this->content_type = isset($_SERVER["CONTENT_TYPE"]) ? $_SERVER["CONTENT_TYPE"] : "";
        $this->params = $this->load_params();
        $this->matches = array();
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
                return json_decode($body, true);
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


}