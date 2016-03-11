<?php
/**
 * Clock Hour Management System
 *
 * @copyright Copyright (c) 2016 Puget Sound Educational Service District
 * @license   Proprietary
 */
namespace CHMS\Common\Repositories\Client;

use CHMS\Common\Repositories\BaseRepository;

class Provider
    extends BaseRepository
    implements Contract
{
    /**
     * @inheritdoc
     */
    public function checkCredentials($credentials = [])
    {
        if (empty($credentials['client_id']) || empty($credentials['client_secret'])) {
            return false;
        }
        $model = $this->findById($credentials['client_id']);
        if ($model === null) {
            return false;
        }
        if (!$model->checkPassword($credentials['client_secret'])) {
            return false;
        }
        return $model;
    }
}
