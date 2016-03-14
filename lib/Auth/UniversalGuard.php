<?php
/**
 * Clock Hour Management System
 *
 * @copyright Copyright (c) 2016 Puget Sound Educational Service District
 * @license   MIT
 */
namespace CHMS\Common\Auth;

use CHMS\Common\Contracts\Acl as AclContract;
use Illuminate\Contracts\Auth\Factory as AuthFactory;

interface UniversalGuard
{
    public function universalUserLogin(AuthFactory $auth);
}