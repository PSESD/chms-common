<?php
/**
 * Clock Hour Management System
 *
 * @copyright Copyright (c) 2016 Puget Sound Educational Service District
 * @license   Proprietary
 */
namespace CHMS\Common\Repositories\User;

use CHMS\Common\Repositories\BaseRepository;

class Provider
    extends BaseRepository
    implements Contract
{
    /**
     * @inheritdoc
     */
    public function getByEmail($email)
    {
      return $this->find([['email', '=', $email]]);
    }

    /**
     * @inheritdoc
     */
    public function checkCredentials($credentials = [])
    {
        if (!isset($credentials['email']) && isset($credentials['user'])) {
            $credentials['email'] = $credentials['user'];
        }
        if (empty($credentials['email']) || empty($credentials['password'])) {
            return false;
        }
        $model = $this->getByEmail($credentials['email']);
        if ($model === null) {
            return false;
        }
        if (!$model->checkPassword($credentials['password'])) {
            return false;
        }
        return $model;
    }
}
