<?php
namespace CHMSTests\Hub\Auth\Contexts;

use CHMS\Hub\Auth\Contexts\Guest as Context;
use CHMSTests\Hub\TestCase;

class GuestTest  extends BaseContextTest
{
    public function getContextClass()
    {
        return Context::class;
    }
}