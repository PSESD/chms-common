<?php
/**
 * Clock Hour Management System
 *
 * @copyright Copyright (c) 2016 Puget Sound Educational Service District
 * @license   Proprietary
 */
namespace CHMS\Common\Repositories\User;

use Illuminate\Database\Eloquent\Model;
use CHMS\Common\Repositories\BaseRepositoryContract;

interface Contract
    extends BaseRepositoryContract
{
    /**
     * Return user by their email
     * @param string $email Email to query
     * @return CHMS\CommonModels\User
     */
    public function getByEmail($email);

    /**
     * Check a user's credentials
     *
     * @param  array $credentials array of credentials (['email' => '', 'password' => ''])
     * @return Model|boolean            Result of credential check, either Model or false
     */
    public function checkCredentials($credentials = []);
}
