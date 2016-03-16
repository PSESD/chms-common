<?php
/**
 * Clock Hour Management System
 *
 * @copyright Copyright (c) 2016 Puget Sound Educational Service District
 * @license   MIT
 */
namespace CHMSTests\Common\Http\Controllers\Base\Stubs;

use CHMS\Hub\Http\Controllers\Base\IndexController as BaseController;
use Illuminate\Http\Request;
use CHMS\Hub\Http\Controllers\Base\IndexActions\GetIndexTrait;
use CHMS\Hub\Http\Controllers\Base\IndexActions\PostIndexTrait;

class IndexController extends BaseController
{
    use GetIndexTrait;
    use PostIndexTrait;
    use ObjectTrait;
}
