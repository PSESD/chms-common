<?php
/**
 * Clock Hour Management System
 *
 * @copyright Copyright (c) 2016 Puget Sound Educational Service District
 * @license   MIT
 */
namespace CHMS\Common\Auth\Contexts;

use CHMS\Common\Contracts\AclContext as AclContextContract;

abstract class BaseContext implements AclContextContract
{
    /**
     * Get the ID of the context
     * @return string
     */
    public function getId()
    {
        return AclContextContract::CONTEXT_ID_PREFIX . $this->id();
    }

    /**
     * Get the non-prefixed ID
     * @return string
     */
    abstract protected function id();
}
