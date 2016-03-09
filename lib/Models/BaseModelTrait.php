<?php
/**
 * Clock Hour Management System - Hub
 *
 * @copyright Copyright (c) 2016 Puget Sound Educational Service District
 * @license   Proprietary
 */
namespace CHMS\Common\Models;

use Canis\Laravel\Db\Behaviors\BlameStampBehavior;

trait BaseModelTrait
{
	use BlameStampBehavior;
}
