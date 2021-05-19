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

        $this->assertEquals(400, $response->getStatusCode());
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

}