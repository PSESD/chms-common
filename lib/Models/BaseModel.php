<?php
/**
 * Clock Hour Management System
 *
 * @copyright Copyright (c) 2016 Puget Sound Educational Service District
 * @license   Proprietary
 */
namespace CHMS\Common\Models;

use Canis\Laravel\Db\Models\RegisteredModel;
use Canis\Laravel\Db\Behaviors\BlameStampBehavior;
use Auth;

class BaseModel
	extends RegisteredModel
{
    use BaseModelTrait;

	/**
	 * @inheritdoc
	 */
	public $timestamps = false;


    public function isAllowed($privilege)
    {
        return Auth::user()->can($privilege, $this);
    }
}
