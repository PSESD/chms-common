<?php
/**
 * Clock Hour Management System - Hub
 *
 * @copyright Copyright (c) 2016 Puget Sound Educational Service District
 * @license   Proprietary
 */
namespace CHMS\Common\Models;

use Canis\Laravel\Db\Behaviors\BlameStampBehavior;
use Auth;

trait BaseModelTrait
{
	use BlameStampBehavior;
    
    public function isAllowed($privilege)
    {
        return Auth::user()->can($privilege, $this);
    }
}
