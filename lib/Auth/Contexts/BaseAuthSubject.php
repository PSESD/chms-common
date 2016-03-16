<?php
/**
 * Clock Hour Management System - Hub
 *
 * @copyright Copyright (c) 2016 Puget Sound Educational Service District
 * @license   Proprietary
 */
namespace CHMS\Hub\Auth\Contexts;

use CHMS\Common\Contracts\Acl as AclContract;
use CHMS\Common\Auth\Contexts\BaseContext;
use CHMS\Common\Auth\RoleBucket;
use Illuminate\Database\Eloquent\Model;

abstract class BaseAuthSubject extends BaseContext
{
    protected $subjectObject;
    protected $modelObject;
    private $roles;

    /**
     * Constructor
     * @param Model  $subjectObject  Auth subject model
     * @param Model  $modelObject    Protected object model
     */
    public function  __construct(Model $subjectObject = null, Model $modelObject = null)
    {
        $this->subjectObject = $subjectObject;
        $this->modelObject = $modelObject;
    }

    /**
     * @inheritdoc
     */
    protected function id()
    {
        $parts = [];
        if (isset($this->subjectObject)) {
            $parts[] = $this->subjectObject->id;
        } else {
            $parts[] = 'guest';
        }
        if (isset($this->modelObject)) {
            $parts[] = $this->modelObject->id;
        }
        return sha1(implode($parts));
    }

    /**
     * @inheritdoc
     */
    public function getRoles()
    {
        if (!isset($this->roles)) {
            $this->roles = new RoleBucket(app(AclContract::class));
            $this->discoverSelf($this->roles);
        }
        return clone $this->roles;
    }

    private function discoverSelf(RoleBucket $roleBucket)
    {
        if ($this->isSelf()) {
            $roleBucket->add('self');
        }
    }

    private function isSelf()
    {
        if (!isset($this->subjectObject) || !isset($this->modelObject)) {
            return false;
        }
        if (isset($this->modelObject->object_id) && $this->modelObject->object_id === $this->subjectObject->id) {
            return true;
        }
        if (isset($this->modelObject->user_id) && $this->modelObject->user_id === $this->subjectObject->id) {
            return true;
        }
        // @todo test for organizational admins
        if (get_class($this->subjectObject) !== get_class($this->modelObject)) {
            return false;
        }
        return $this->subjectObject->id === $this->modelObject->id;
    }
}
