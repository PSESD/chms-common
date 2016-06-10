<?php
/**
 * Clock Hour Management System
 *
 * @copyright Copyright (c) 2016 Puget Sound Educational Service District
 * @license   MIT
 */
namespace CHMS\Common\Auth;

use Authorizer;
use CHMS\Common\Http\RequestClient;

class SelfSignedRequest extends RequestClient
{
    static private $requestGenerator;

    protected function getRequestGenerator()
    {
        if (!isset(self::$requestGenerator)) {
            $grantor = Authorizer::getIssuer()->getGrantType('manual_client');
            $requestGenerator = $grantor->completeFlow();
            if (is_callable($requestGenerator)) {
                self::$requestGenerator = $requestGenerator;
            }
        }
        return self::$requestGenerator;
    }
}