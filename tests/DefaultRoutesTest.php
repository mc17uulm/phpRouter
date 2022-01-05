<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

final class DefaultRoutesTest extends TestCase
{

    public static function get_client() : Client {
        return new Client(['base_uri' => 'http://localhost', 'http_errors' => false]);
    }

    /**
     * @test
     * @throws GuzzleException
     */
    public function canRouteToIndex() : void {
        $client = self::get_client();
        $response = $client->request('GET', '/');

        $this->assertEquals(200, $response->getStatusCode());

        $this->assertEquals("ok", $response->getBody()->getContents());

    }

    /**
     * @test
     * @throws GuzzleException
     */
    public function canHandleError() : void {

        $client = self::get_client();
        $response = $client->request('GET', '/error');

        $this->assertEquals(500, $response->getStatusCode());
    }

    /**
     * @test
     * @throws GuzzleException
     */
    public function correctNotFound() : void {
        $client = self::get_client();
        $response = $client->request('GET', "not-found");

        $this->assertEquals(404, $response->getStatusCode());
        $this->assertEquals('{"status":"error","message":"not found"}', $response->getBody()->getContents());
    }

    /**
     * @test
     * @throws GuzzleException
     */
    public function canServeFiles() : void {
        $client = self::get_client();
        $response = $client->request('GET', 'dist/info.txt');

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('text/plain;charset=UTF-8', $response->getHeader('Content-Type')[0]);
        $this->assertEquals("Hallo", $response->getBody()->getContents());
    }

    /**
     * @test
     * @throws GuzzleException
     */
    public function canDetectParam() : void {
        $client = self::get_client();
        $response = $client->request('GET', 'param/123');

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('123', $response->getBody()->getContents());
    }

    /**
     * @test
     * @throws GuzzleException
     */
    public function canDetectMultipleParams() : void {
        $client = self::get_client();
        $response = $client->request('GET', 'params/123/teststring');

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('teststring: 123', $response->getBody()->getContents());
    }

    /**
     * @test
     * @throws GuzzleException
     */
    public function localMiddlewareIsWorking() : void {
        $client = self::get_client();
        $response = $client->request('GET', 'login');

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('ok', $response->getBody()->getContents());
        $this->assertEquals('application/json', $response->getHeader('Content-Type')[0]);
    }

    /**
     * @test
     * @throws GuzzleException
     */
    public function globalMiddlewareIsWorking() : void {
        $client = self::get_client();
        $response = $client->request('GET', '/');
        $this->assertEquals('true', $response->getHeader('X-Modified-Header')[0]);
    }

    /**
     * @throws GuzzleException
     */
    public function arrayCallableIsWorking() : void {
        $client = self::get_client();
        $response = $client->request('GET', 'callable_router');
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('ok callable', $response->getBody()->getContents());
    }

    /**
     * @throws GuzzleException
     */
    public function dynamicMiddlewareIsWorking() : void {
        $client = self::get_client();
        $response_ok = $client->request('GET', 'dynamic/5');
        $response_error = $client->request('GET', 'dynamic/12');
        $this->assertEquals(200, $response_ok->getStatusCode());
        $this->assertEquals('ok', $response_ok->getBody()->getContents());
        $this->assertEquals(404, $response_error->getStatusCode());
    }

    /**
     * @throws GuzzleException
     */
    public function routerGroupIsWorking() : void {
        $client = self::get_client();
        $response = $client->request('GET', 'api/test');

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('test', $response->getBody()->getContents());
    }

    /**
     * @throws GuzzleException
     */
    public function routerGroupNotOverwriting() : void {
        $client = self::get_client();
        $response = $client->request('GET', 'api');

        $this->assertEquals(404, $response->getStatusCode());
    }

}