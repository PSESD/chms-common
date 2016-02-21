<?php
/**
 * Clock Hour Management System
 *
 * @copyright Copyright (c) 2016 Puget Sound Educational Service District
 * @license   MIT
 */
namespace CHMS\Common\Contracts;

interface AclContext
{
    const CONTEXT_ID_PREFIX = 'ctx:';

    /**
     * Returns the id for the context
     *
     * @return array
     */
    public function getId();

    /**
     * Returns the roles for the context
     *
     * @return array
     */
    public function getRoles();
}
