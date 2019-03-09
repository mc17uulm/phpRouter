<?php
/**
 * Created by PhpStorm.
 * User: mc17uulm
 * Date: 03.03.2019
 * Time: 14:30
 */

namespace PHPRouting\routing\response;

class Response
{

    public function set_code(int $code) : void
    {
        http_response_code($code);
    }

    public function add_header(string $key, string $value) : void
    {
        header("$key: $value");
    }

    public function set_content_type(string $type) : void
    {
        $this->add_header("content-type", $type);
    }

    public function send($msg) : void
    {
        if(is_array($msg))
        {
            $msg = json_encode($msg);
        }

        die($msg);
    }

    public function send_error($msg = "") : void
    {
        $this->set_content_type("application/json");
        $this->send(new Error($msg));
    }

    public function send_success($msg = "") : void
    {
        $this->set_content_type("application/json");
        $this->send(new Success($msg));
    }

    public function send_file(string $file) : void
    {
        if(file_exists($file))
        {
            $this->set_content_type(Response::get_mime_type($file));
            die(file_get_contents($file));
        }

        die("File $file not found");
    }

    public function set_type_to_json() : void
    {
        $this->set_content_type("application/json");
    }

    public function redirect()
    {
        $this->add_header("Location", "/login");
        die();
    }

    public static function get_mime_type(string $file) : string
    {
        $f = finfo_open();
        $info = finfo_file($f, $file, FILEINFO_MIME_TYPE);
        finfo_close($f);
        return $info;

    }

}