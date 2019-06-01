<?php
/**
 * Created by PhpStorm.
 * User: mc17uulm
 * Date: 03.03.2019
 * Time: 14:30
 */

namespace PHPRouting\routing\response;

use PHPRouting\routing\views\Viewable;

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

    public function parse_php_file(string $file, array $params = array()) : void
    {
        if(file_exists($file))
        {
            $this->set_content_type(Response::get_mime_type($file));
            ob_start();
            include $file;
            $data = ob_get_clean();
            die($data);
        }

        die("File $file not found");
    }

    public function set_type_to_json() : void
    {
        $this->set_content_type("application/json");
    }

    public function redirect(string $location = "/") : void
    {
        $this->add_header("Location", $location);
        die();
    }

    public function render(Viewable $template) : void
    {
        $template->render();
        die();
    }

    public static function get_mime_type(string $file) : string
    {
        $mime_types = array(

            'txt' => 'text/plain',
            'htm' => 'text/html',
            'html' => 'text/html',
            'php' => 'text/html',
            'css' => 'text/css',
            'js' => 'application/javascript',
            'json' => 'application/json',
            'xml' => 'application/xml',
            'swf' => 'application/x-shockwave-flash',
            'flv' => 'video/x-flv',

            // images
            'png' => 'image/png',
            'jpe' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'jpg' => 'image/jpeg',
            'gif' => 'image/gif',
            'bmp' => 'image/bmp',
            'ico' => 'image/vnd.microsoft.icon',
            'tiff' => 'image/tiff',
            'tif' => 'image/tiff',
            'svg' => 'image/svg+xml',
            'svgz' => 'image/svg+xml',

            // archives
            'zip' => 'application/zip',
            'rar' => 'application/x-rar-compressed',
            'exe' => 'application/x-msdownload',
            'msi' => 'application/x-msdownload',
            'cab' => 'application/vnd.ms-cab-compressed',

            // audio/video
            'mp3' => 'audio/mpeg',
            'qt' => 'video/quicktime',
            'mov' => 'video/quicktime',

            // adobe
            'pdf' => 'application/pdf',
            'psd' => 'image/vnd.adobe.photoshop',
            'ai' => 'application/postscript',
            'eps' => 'application/postscript',
            'ps' => 'application/postscript',

            // ms office
            'doc' => 'application/msword',
            'rtf' => 'application/rtf',
            'xls' => 'application/vnd.ms-excel',
            'ppt' => 'application/vnd.ms-powerpoint',

            // open office
            'odt' => 'application/vnd.oasis.opendocument.text',
            'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
        );

        $t = explode('.',$file);
        $ext = strtolower(array_pop($t));
        if (array_key_exists($ext, $mime_types)) {
            return $mime_types[$ext];
        }
        elseif (function_exists('finfo_open')) {
            $finfo = finfo_open(FILEINFO_MIME);
            $mimetype = finfo_file($finfo, $file);
            finfo_close($finfo);
            return $mimetype;
        }
        else {
            return 'application/octet-stream';
        }

    }

}