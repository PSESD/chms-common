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

    
    protected function getToken()
    {
        if (!isset(self::$token)) {
            $grantor = Authorizer::getIssuer()->getGrantType('manual_client');
            $accessToken = $grantor->completeFlow();
            if (!empty($accessToken)) {
                self::$token = $accessToken->getId();
            }
        }
        return self::$token;
    }
}