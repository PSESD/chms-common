<?php
/**
 * Clock Hour Management System
 *
 * @copyright Copyright (c) 2016 Puget Sound Educational Service District
 * @license   MIT
 */
namespace CHMS\Common\Auth;

use CHMS\Common\Contracts\Context as ContextContract;

class Context implements ContextContract
{
    public function getContextFields()
    {
        return [];
    }
}