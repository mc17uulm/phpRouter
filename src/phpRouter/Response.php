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

    /**
     * @param View $view
     */
    public function show(View $view) : void {
        if($this->debug) {
            header("Access-Control-Allow-Origin: *");
            header("Access-Control-Allow-Methods: POST");
        }
        http_response_code(200);
        header("Content-Type: text/html");
        $view->show();
        die();
    }

    /**
     * @param mixed $data
     */
    public function send($data = "") : void
    {
        if($this->debug) {
            header("Access-Control-Allow-Origin: *");
            header("Access-Control-Allow-Methods: POST");
        }

        $this->headers["Content-Type"] = $this->content_type;
        foreach($this->headers as $key => $value) {
            header("$key: $value");
        }
        http_response_code($this->code);

        if($this->content_type === "application/json") {
            echo json_encode($data);
        } else {
            echo $data;
        }
        die();
    }

    /**
     * @param string | mixed $data
     */
    public function send_success($data = "") : void {
        $this->set_http_code(200);
        $this->set_content_type("application/json");
        $this->send([
            "status" => "success",
            "data" => $data
        ]);
    }

    /**
     * @param string $message
     * @param string $debug_message
     */
    public function send_error(string $message, string $debug_message = "") : void
    {
        $this->set_http_code(200);
        $this->set_content_type("application/json");
        $response = [
            "status" => "error",
            "message" => $message
        ];
        if($this->debug) {
            $response["debug_message"] = $debug_message;
        }
        $this->send($response);
    }

    /**
     * @param SendableException $e
     * @param bool $debug
     */
    public static function send_exception(SendableException $e, bool $debug = false) : void {
        $response = new Response($debug);
        $response->send_error($e->getMessage(), $e->get_debug_message());
    }

    /**
     * @param string $url
     */
    public function redirect(string $url) : void {
        header("Location: $url");
        die();
    }

}