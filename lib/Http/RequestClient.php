<?php
/**
 * Clock Hour Management System
 *
 * @copyright Copyright (c) 2016 Puget Sound Educational Service District
 * @license   MIT
 */
namespace CHMS\Common\Http;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Client as GuzzleClient;
use Art4\JsonApiClient\Utils\Manager as JsonManager;
use Art4\JsonApiClient\Exception\ValidationException;

class RequestClient
{
    private $client;

    public function getRequest($method, $uri, $options = [])
    {
        $rg = $this->getRequestGenerator();
        return $rg($method, $uri, $options);
    }

    protected function getRequestGenerator()
    {
        return function ($method, $uri, $options = []) {
            return new Request($method, $uri, $options);
        };
    }

    public function parse(Response $response)
    {
        if ($response->getStatusCode() !== 200) {
            return false;
        }
        $jsonResponse = $response->getBody()->getContents();
        \dump($jsonResponse);exit;
        
        $jsonapi = new JsonManager;
        try {
            $parsed = $jsonapi->parse($jsonResponse);
        } catch (ValidationException $e) {
            return false;
        }
        return $parsed;
    }

    public function makeParse(Request $request)
    {
        $response = $this->getClient()->send($request);
        return $this->parse($response);
    }

    public function make(Request $request)
    {
        return $this->getClient()->send($request);
    }

    protected function getClient()
    {
        if (!isset($this->client)) {
          $this->client = new GuzzleClient([
              'timeout'  => 5,
              'http_errors' => false,
              'connect_timeout' => 15
          ]);
        }
        return $this->client;
    }
}
