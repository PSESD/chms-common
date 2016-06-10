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
use CHMS\Common\Exceptions\UnprocessableEntityHttpException;
use CHMS\Common\Contracts\InputGate as InputGateContract;

trait PatchObjectTrait
{
    /**
     * Update the object
     *
     * @param  Request  $request Request object
     * @param  string $id Unique identifier for the object
     * @return ResponseInterface    Response for object
     */
    public function patch(Request $request, Fractal $fractal, InputGateContract $inputGate, $id)
    {
        $model = $this->loadAuthorizeObject($id, 'update');
        $input = $request->json()->all();
        if (!is_array($input) || empty($input)) {
            throw new UnprocessableEntityHttpException("Invalid request body");
        }
        $attributes = $originalAttributes = array_get($input, 'data.attributes', []);
        $relationships = array_get($input, 'data.relationships', []);
        $attributes = $inputGate->process($model, $attributes, 'update');
        $validatedRelationshipData = $this->validateRelationshipData($model, $relationships, 'update');
        // do update logic
        if ($model->update($attributes) && $this->saveRelationshipData($validatedRelationshipData)) {
            return $this->respondWithUpdated(
                $fractal->createData(new FractalItem($this->loadAuthorizeObject($id, 'update'), $this->getTransformer(), $this->getResourceKey()))
            );
        }
        
        throw ServerErrorHttpException("Unable to save object. Try again later.");
    }

}
?>