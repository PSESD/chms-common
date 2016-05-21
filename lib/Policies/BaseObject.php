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
        $acl = app(AclContract::class);
        $acl->switchObjectContext($arguments[1]);
        $result = false;
        if ($acl->isAllowedModel(get_class($arguments[1]), $method)) {
            $result = true;
        }
        $acl->switchObjectContext(null);
        return $result;
    }
}
