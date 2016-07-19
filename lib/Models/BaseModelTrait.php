<?php
/**
 * Clock Hour Management System - Hub
 *
 * @copyright Copyright (c) 2016 Puget Sound Educational Service District
 * @license   Proprietary
 */
namespace CHMS\Common\Models;

use Canis\Laravel\Db\Behaviors\BlameStampBehavior;
use Canis\Laravel\Db\Behaviors\MetaBehavior;
use Auth;

trait BaseModelTrait
{
	use BlameStampBehavior;
    use MetaBehavior;
    
    public function isAllowed($privilege)
    {
        if ($privilege === 'update') {
            $privilege = 'set';
        }
        return Auth::user()->can($privilege, $this);
    }

    public function getFields()
    {
        return array_merge($this->getVirtualFields(), $this->getTableColumns());
    }
    
    public function getVirtualFields()
    {
        return [];
    }
}
