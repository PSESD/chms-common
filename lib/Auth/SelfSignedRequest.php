<?php
/**
 * Clock Hour Management System
 *
 * @copyright Copyright (c) 2016 Puget Sound Educational Service District
 * @license   MIT
 */
namespace CHMS\Common\Auth;

use Authorizer;
use CHMS\Common\Contracts\Acl as AclContract;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Client as GuzzleClient;

class SelfSignedRequest
{
    static private $token;
    protected $request;

    public function make()
    {
        return $this->getclient()->make($this->getRequest());
    }

    public function getRequest()
    {
        if (!isset($this->request)) {
            $this->request = new Request;
        }
        return $this->request;
    }

    protected function getToken()
    {
        if (!isset(self::$token)) {
            $grantor = Authorizer::getIssuer()->getGrantType('manual_client');
            $accessToken = $grantor->completeFlow();
                \dump($accessToken);exit;
            if (!empty($accessToken)) {
                self::$token = $accessToken->getId();
            }
        }
        return self::$token;
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