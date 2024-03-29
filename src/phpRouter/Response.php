<?php

namespace phpRouter;

use Jenssegers\Blade\Blade;
use JetBrains\PhpStorm\NoReturn;

/**
 * Class Response
 * @package phpRouter
 */
final class Response
{

    /**
     * @var array<string, string>
     */
    private array $headers;
    /**
     * @var string
     */
    private string $content_type;
    /**
     * @var bool
     */
    private bool $debug;
    /**
     * @var Blade | null
     */
    private ?Blade $blade;

    /**
     * Response constructor.
     * @param bool $debug
     * @param Blade|null $blade
     */
    public function __construct(bool $debug = false, ?Blade $blade = null)
    {
        $this->headers = [];
        $this->content_type = "text/html";
        $this->debug = $debug;
        $this->blade = $blade;
    }

    /**
     * @param string $key
     * @param string $value
     */
    public function add_header(string $key, string $value) : void {
        $this->headers += array($key => $value);
    }

    /**
     * @param string $content_type
     */
    public function set_content_type(string $content_type) : void {
        $this->content_type = $content_type;
    }

    private function send_headers() : void {
        foreach($this->headers as $key => $value) {
            header("$key: $value");
        }
    }

    /**
     * @param mixed $data
     * @param int $code
     */
    #[NoReturn]
    public function send(mixed $data = "", int $code = 200) : void
    {
        http_response_code($code);
        if($this->debug) {
            $this->add_header('Access-Control-Allow-Origin', '*');
            $this->add_header('Access-Control-Allow-Methods', 'POST');
        }

        $this->add_header('Content-Type', $this->content_type);
        $this->send_headers();
        echo $data;
        die();
    }

    /**
     * @param mixed $data
     * @param int $code
     */
    #[NoReturn]
    public function send_success(mixed $data = "", int $code = 200) : void {
        $this->set_content_type("application/json");
        $this->send(json_encode($data), $code);
    }

    /**
     * @param string $message
     * @param string $debug_message
     * @param int $code
     */
    #[NoReturn]
    public function send_error(string $message, string $debug_message = "", int $code = 400) : void
    {
        $this->set_content_type("application/json");
        $response = [
            "status" => "error",
            "message" => $message
        ];
        if($this->debug) {
            $response["debug"] = $debug_message;
        }
        $this->send(json_encode($response), $code);
    }

    /**
     * @param SendableException $e
     * @param int $code
     */
    #[NoReturn]
    public function send_exception(SendableException $e, int $code = 500) : void {
        $this->send_error($e->get_public_message(), $e->getMessage(), $code);
    }

    /**
     * @param callable $edit
     */
    public function setup_blade(callable $edit) : void {
        $this->blade = $edit($this->blade);
    }

    /**
     * @param string $name
     * @param array $content
     * @param int $code
     */
    #[NoReturn]
    public function view(string $name, array $content, int $code = 200) : void {
        $this->blade($name, $content, $code);
    }

    /**
     * @param string $name
     * @param array $content
     * @param int $code
     */
    #[NoReturn]
    public function blade(string $name, array $content, int $code = 200) : void {
        http_response_code($code);
        if($this->debug) {
            header("Access-Control-Allow-Origin: *");
            header("Access-Control-Allow-Methods: POST");
        }
        $this->set_content_type('text/html');
        $this->send_headers();
        echo $this->blade->render($name, $content);
        die();
    }

    /**
     * @param string $url
     */
    #[NoReturn]
    public function redirect(string $url) : void {
        header("Location: $url");
        die();
    }

}