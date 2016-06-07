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
        return Auth::user()->can($privilege, $this);
    }
}
