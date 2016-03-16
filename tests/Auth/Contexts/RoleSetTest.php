<?php
namespace CHMSTests\Common\Auth\Contexts;

use CHMS\Hub\Auth\Contexts\RoleSet as Context;

class RoleSetTest extends BaseContextTest
{
    public function getContextClass()
    {
        return Context::class;
    }
}