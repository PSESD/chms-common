<?php
/**
 * Clock Hour Management System
 *
 * @copyright Copyright (c) 2016 Puget Sound Educational Service District
 * @license   MIT
 */
namespace CHMS\Common\Http\Controllers\Base;

use Illuminate\Database\Eloquent\Model;
use CHMS\Common\Http\Controllers\Controller as BaseController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

abstract class ObjectController
    extends BaseController
    implements ControllerInterface
{
    /**
     * Get the object and check to make sure the authorized user has access
     * @param  string|int    $id
     * @param  string        $action Action to be taken (read, update, delete)
     * @throws NotFoundHttpException        Model could not be found
     * @throws AccessDeniedHttpException    User is not allowed to perform the action with this model
     * @return Model         Loaded model
     */
    protected function loadAuthorizeObject($id, $action)
    {
        $model = $this->getRepository()->findById($id);
        if (empty($model)) {
            throw new NotFoundHttpException("Object not found");
        }
        if (!$model->isAllowed($action)) {
            throw new AccessDeniedHttpException("Forbidden");
        }
        return $model;
    }
}
