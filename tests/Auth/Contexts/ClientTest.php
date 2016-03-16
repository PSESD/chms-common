<?php
namespace CHMSTests\Hub\Auth\Contexts;

use CHMS\Hub\Auth\Contexts\Client as Context;

class ClientTest extends BaseContextTest
{
    public function getContextClass()
    {
        return Context::class;
    }
}