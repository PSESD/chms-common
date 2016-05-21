<?php
/**
 * Clock Hour Management System
 *
 * @copyright Copyright (c) 2016 Puget Sound Educational Service District
 * @license   MIT
 */
namespace CHMS\Common\Http\Controllers\Base\ObjectActions;

use Illuminate\Http\Request;
use League\Fractal\Manager as Fractal;
use League\Fractal\Resource\Item as FractalItem;

trait HeadObjectTrait
{
     /**
     * Check if the object exists
     *
     * @param  Request  $request Request object
     * @param  string $id Unique identifier for the object
     * @return ResponseInterface    Response for object
     */
    public function head(Request $request, Fractal $fractal, $id)
    {
        $model = $this->loadAuthorizeObject($id, 'read');
        return $this->respondWithEmptyItem(
            $fractal->createData($this->getFractalItem($model))
        );
    }
}
?>