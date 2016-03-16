<?php
/**
 * Clock Hour Management System
 *
 * @copyright Copyright (c) 2016 Puget Sound Educational Service District
 * @license   MIT
 */
namespace CHMS\Common\Policies;

use CHMS\Common\Contracts\Acl as AclContract;

class BaseObject extends BasePolicy
{
    public function __call($method, $arguments)
    {
        if (!app(AclContract::class)->isAllowedModel(get_class($arguments[1]), $method)) {
            return false;
        }
        return true;
    }
}
