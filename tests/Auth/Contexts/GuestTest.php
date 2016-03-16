<?php
namespace CHMSTests\Common\Auth\Contexts;

use CHMS\Hub\Auth\Contexts\Guest as Context;
use CHMSTests\Common\TestCase;

class GuestTest  extends BaseContextTest
{
    public function getContextClass()
    {
        return Context::class;
    }
}