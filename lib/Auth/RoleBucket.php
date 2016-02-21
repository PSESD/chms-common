<?php
/**
 * Clock Hour Management System
 *
 * @copyright Copyright (c) 2016 Puget Sound Educational Service District
 * @license   MIT
 */
namespace CHMS\Common\Auth;

use CHMS\Common\Contracts\Acl as AclContract;

class RoleBucket
{
    /**
     * @var AclContract
     */
    private $acl;

    /**
     * @var array
     */
    private $bucket = [];

    /**
     * Constructor
     * @param AclContract $acl
     */
    public function __construct(AclContract $acl)
    {
        $this->acl = $acl;
    }

    /**
     * Add role
     * @param string  $role
     * @return object (self)
     */
    public function add($role)
    {
        $this->bucket[$role] = sprintf("%05d", $this->acl->getRoleLevel($role)) .'-'. $role;
        return $this;
    }

    /**
     * Remove role
     * @param  string $role
     * @return object (self)
     */
    public function remove($role)
    {
        unset($this->bucket[$role]);
        return $this;
    }

    /**
     * Return array form of bucket
     * @return array
     */
    public function toArray()
    {
        $bucket = $this->bucket;
        arsort($bucket);
        return array_keys($bucket);
    }
}
