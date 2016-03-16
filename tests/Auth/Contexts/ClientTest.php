<?php
namespace CHMSTests\Common\Auth\Contexts;

use CHMS\Hub\Auth\Contexts\Client as Context;

class ClientTest extends BaseContextTest
{
    public function getContextClass()
    {
        return Context::class;
    }
}