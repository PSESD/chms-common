<?php
/**
 * Clock Hour Management System
 *
 * @copyright Copyright (c) 2016 Puget Sound Educational Service District
 * @license   MIT
 */
namespace CHMS\Common\Http\Controllers\Base\IndexActions;

use Illuminate\Http\Request;
use League\Fractal\Manager as Fractal;
use League\Fractal\Resource\Item as FractalItem;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use CHMS\Common\Contracts\InputGate as InputGateContract;
use CHMS\Common\Exceptions\ServerErrorHttpException;
use CHMS\Common\Exceptions\UnprocessableEntityHttpException;

trait PostIndexTrait
{
    /**
     * Create a new object
     *
     * @param  Request  $request Request object
     * @return ResponseInterface Response for request
     */
    public function post(Request $request, Fractal $fractal, InputGateContract $inputGate)
    {
        $input = $request->json()->all();
        if (!is_array($input) || empty($input)) {
            throw new UnprocessableEntityHttpException("Invalid request body");
        }
        $attributes = $originalAttributes = array_get($input, 'data.attributes', []);
        $relationships = array_get($input, 'data.relationships', []);
        $model = $this->getRepository()->model();
        $attributes = $inputGate->process($model, $attributes, 'create');
        $validatedRelationshipData = $this->validateRelationshipData($model, $relationships, 'create');
        if (!empty($attributes) 
            && ($model = $this->getRepository()->create($attributes))
            && $this->saveRelationshipData($validatedRelationshipData)
        ) {
            return $this->respondWithCreated(
                $fractal->createData(new FractalItem($this->getRepository()->findById($model->id), $this->getTransformer(), $this->getResourceKey()))
            );
        }
        throw ServerErrorHttpException("Unable to save object. Try again later.");
    }
}
?>