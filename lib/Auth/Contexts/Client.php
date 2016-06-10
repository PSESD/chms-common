<?php
/**
 * Clock Hour Management System
 *
 * @copyright Copyright (c) 2016 Puget Sound Educational Service District
 * @license   MIT
 */
namespace CHMS\Common\Auth\Contexts;

use CHMS\Common\Models\Client as ClientModel;
use CHMS\Common\Auth\RoleBucket;

class Client extends BaseAuthSubject
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
            if ($this->subjectObject->type === ClientModel::TYPE_CENTRAL_HUB) {
                $this->roles->add('client_central_hub');
            } elseif ($this->subjectObject->type === ClientModel::TYPE_PROVIDER) {
                $this->roles->add('client_provider');
            } elseif ($this->subjectObject->type === ClientModel::TYPE_PROVIDER_HUB) {
                $this->roles->add('client_provider_hub');
            } elseif ($this->subjectObject->type === ClientModel::TYPE_CLIENT) {
                $this->roles->add('client_client');
            }
        }
        return clone $this->roles;
    }
}
