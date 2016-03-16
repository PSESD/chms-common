<?php
/**
 * Clock Hour Management System
 *
 * @copyright Copyright (c) 2016 Puget Sound Educational Service District
 * @license   MIT
 */
namespace CHMSTests\Common\Http\Controllers\Base\Stubs;

use CHMS\Hub\Http\Controllers\Base\ObjectController as BaseController;
use Illuminate\Http\Request;
use CHMS\Hub\Http\Controllers\Base\ObjectActions\GetObjectTrait;
use CHMS\Hub\Http\Controllers\Base\ObjectActions\HeadObjectTrait;
use CHMS\Hub\Http\Controllers\Base\ObjectActions\PatchObjectTrait;
use CHMS\Hub\Http\Controllers\Base\ObjectActions\DeleteObjectTrait;

class ObjectController extends BaseController
{
    use ObjectTrait;
    use GetObjectTrait;
    use HeadObjectTrait;
    use PatchObjectTrait;
    use DeleteObjectTrait;
}
