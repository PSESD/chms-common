<?php
/**
 * Clock Hour Management System
 *
 * @copyright Copyright (c) 2016 Puget Sound Educational Service District
 * @license   MIT
 */
namespace CHMS\Common\Contracts;

interface Acl
{
    /**
     * Set up roles in the application
     * @return boolean
     */
    public function setupRoles();

    /**
     * Get role level
     * @param  string $id
     * @return int
     */
    public function getRoleLevel($id);
}
