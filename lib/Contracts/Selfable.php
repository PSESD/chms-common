<?php
/**
 * Clock Hour Management System
 *
 * @copyright Copyright (c) 2016 Puget Sound Educational Service District
 * @license   MIT
 */
namespace CHMS\Common\Contracts;

use CHMS\Common\Exceptions\InvalidInputException;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;

interface Selfable
{
    public function isSelfOwned(AuthorizableContract $user);
}
