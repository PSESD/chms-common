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

trait GetObjectTrait
{
    /**
     * Get the object
     *
     * @param  Request  $request Request object
     * @param  string $id Unique identifier for the object
     * @return ResponseInterface    Response for object
     */
    public function get(Request $request, Fractal $fractal, $id)
    {
        $model = $this->loadAuthorizeObject($id, 'read');
        $include = $request->input('include', '');
        return $this->respondWithItem(
            $fractal->createData(new FractalItem($model, $this->getTransformer(), $this->getResourceKey()))
        );
    }
}
?>