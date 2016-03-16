<?php
/**
 * Clock Hour Management System
 *
 * @copyright Copyright (c) 2016 Puget Sound Educational Service District
 * @license   MIT
 */
namespace CHMS\Common\Http\Controllers\Base;

use Illuminate\Database\Eloquent\Model;
use CHMS\Common\Http\Controllers\Base\ObjectController as BaseObjectController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

abstract class BaseRelationController
    extends BaseObjectController
    implements ControllerInterface
{
}
