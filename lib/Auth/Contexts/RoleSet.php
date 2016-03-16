<?php
/**
 * Clock Hour Management System
 *
 * @copyright Copyright (c) 2016 Puget Sound Educational Service District
 * @license   MIT
 */
namespace CHMS\Common\Auth\Contexts;

use CHMS\Common\Auth\Contexts\BaseContext;
use CHMS\Common\Auth\RoleBucket;

class RoleSet extends BaseContext
{
    /**
    * @var RoleBucket
    */
    private $roles;

    /**
     * Prophecy\Doubler\ClassPatch\DisableConstructorPatch
     * @param RoleBucket $roles
     */
    public function  __construct(RoleBucket $roles)
    {
        $this->roles = $roles;
    }

    /**
     * @inheritdoc
     */
    protected function id()
    {
        return sha1(implode($this->roles->toArray()));
    }

    /**
     * @inheritdoc
     */
    public function getRoles()
    {
        return clone $this->roles;
    }

}
