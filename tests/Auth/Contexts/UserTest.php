<?php
namespace CHMSTests\Hub\Auth\Contexts;

use CHMS\Hub\Auth\Contexts\User as Context;
use CHMSTests\Hub\TestCase;

class UserTest extends BaseContextTest
{
    public function getContextClass()
    {
        return Context::class;
    }
}