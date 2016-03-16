<?php
/**
 * Clock Hour Management System
 *
 * @copyright Copyright (c) 2016 Puget Sound Educational Service District
 * @license   MIT
 */
namespace CHMS\Common\Auth\Contexts;

use CHMS\Common\Auth\Contexts\BaseContext;

class Guest extends BaseAuthSubject
{
    /**
     * @var RollBucket
     */
    private $roles;

    /**
     * @inheritdoc
     */
    public function getRoles()
    {
        if (!isset($this->roles)) {
            $this->roles = parent::getRoles();
            $this->roles->add('guest');
        }
        return clone $this->roles;
    }
}
