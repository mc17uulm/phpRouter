<?php

namespace phpRouter;

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
     * @var int
     */
    private int $code;
    /**
     * @var string
     */
    private string $content_type;
    /**
     * @var bool
     */
    private bool $debug;

    public function __construct(bool $debug = false)
    {
        $this->headers = [];
        $this->code = 200;
        $this->content_type = "text/html";
        $this->debug = $debug;
    }

    /**
     * @param string $key
     * @param string $value
     */
    public function add_header(string $key, string $value) : void {
        array_push($this->headers, array($key => $value));
    }

    /**
     * @param int $code
     */
    public function set_http_code(int $code) : void {
        $this->code = $code;
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
     * @param View $view
     */
    public function show(View $view) : void {
        $this->render($view);
    }

    /**
     * @param mixed $data
     */
    public function send($data = "") : void
    {
        http_response_code($this->code);
        if($this->debug) {
            header("Access-Control-Allow-Origin: *");
            header("Access-Control-Allow-Methods: POST");
        }

        $this->headers["Content-Type"] = $this->content_type;
        $this->send_headers();
        echo $data;
        die();
    }

    /**
     * @param string | mixed $data
     */
    public function send_success($data = "") : void {
        $this->set_content_type("application/json");
        $this->send(json_encode($data));
    }

    /**
     * @param string $message
     * @param string $debug_message
     * @param int $code
     */
    public function send_error(string $message, string $debug_message = "", int $code = 400) : void
    {
        $this->set_http_code($code);
        $this->set_content_type("application/json");
        $response = [
            "status" => "error",
            "message" => $message
        ];
        if($this->debug) {
            $response["debug_message"] = $debug_message;
        }
        $this->send(json_encode($response));
    }

    /**
     * @param SendableException $e
     */
    public function send_exception(SendableException $e) : void {
        $this->send_error($e->get_public_message(), $e->getMessage());
    }

    /**
     * @param View $view
     * @param int $code
     */
    public function render(View $view, int $code = 200) : void {
        if($this->debug) {
            header("Access-Control-Allow-Origin: *");
            header("Access-Control-Allow-Methods: POST");
        }
        http_response_code($code);
        $this->set_content_type('text/html');
        $this->send_headers();
        $view->show();
        die();
    }

    /**
     * @param string $url
     */
    public function redirect(string $url) : void {
        header("Location: $url");
        die();
    }

}