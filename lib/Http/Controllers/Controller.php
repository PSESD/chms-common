<?php
/**
 * Clock Hour Management System
 *
 * @copyright Copyright (c) 2016 Puget Sound Educational Service District
 * @license   MIT
 */
namespace CHMS\Common\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Collection;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Illuminate\Database\Eloquent\Model;
use League\Fractal\Scope as Fractal;
use CHMS\Common\Http\Relationships\ValidatedRelationshipData;

abstract class Controller extends BaseController
{
    public function __construct()
    {
    }

    public function relationshipHandlers()
    {
        return [];
    }

    public function getRelationshipHandlers()
    {
        $handlers = [];
        foreach ($this->relationshipHandlers() as $key => $handlerClass) {
            $handlers[$key] = new $handlerClass;
        }
        return $handlers;
    }

    public function validateRelationshipData(Model $model, array $data, $scope)
    {
        $relationshipHandlers = $this->getRelationshipHandlers();
        $validatedRelationshipData = new ValidatedRelationshipData;
        foreach ($data as $relationshipType => $relationshipData) {
            if (!isset($relationshipHandlers[$relationshipType])) {
                throw new BadRequestHttpException("Relationship type '{$relationshipType}' is not valid");
            }
            $handler = $relationshipHandlers[$relationshipType];
            $handler->validate($model, $relationshipData, $scope, true);
            $validatedRelationshipData->addTasks($relationshipType, function() use ($handler, $model, $relationshipData, $scope) {
                return $handler->handle($model, $relationshipData, $scope);
            });
        }
        return $validatedRelationshipData;
    }

    public function saveRelationshipData(ValidatedRelationshipData $data)
    {
        foreach ($data->getHandleTasks() as $task) {
            if (!$task()) {
                return false;
            }
        }
        return true;
    }

    public function getDefaultHeaders()
    {
        return [
            'Content-Type' => 'application/vnd.api+json'
        ];
    }

    public function respond($package, $statusCode = 200, $headers = [])
    {
        if ($package instanceof Fractal) {
            $package = $package->toArray();
        }
        return response()->json($package, $statusCode, array_merge($this->getDefaultHeaders(), $headers));
    }

    public function respondWithItem(Fractal $package, $statusCode = 200, $headers = [], $clear = false)
    {
        $package = $package->toArray();
        $headers['X-Chms-Object-Id'] = $package['data']['id'];
        $headers['X-Chms-Object-Type'] = $package['data']['type'];
        if ($clear) {
            return $this->respondWithStatus($statusCode, $headers);
        } else {
            return $this->respond($package, $statusCode, $headers);
        }
    }

    public function respondWithEmptyItem(Fractal $package, $statusCode = 200, $headers = [])
    {
        return $this->respondWithItem($package, $statusCode, $headers, true);
    }

    public function respondWithCollection(Fractal $package, $statusCode = 200, $headers = [])
    {
        return $this->respond($package, $statusCode, $headers);
    }

    public function respondWithUpdated(Fractal $package, $statusCode = 200, $headers = [])
    {
        return $this->respondWithItem($package, $statusCode, $headers);
    }

    public function respondWithCreated(Fractal $package, $statusCode = 201, $headers = [])
    {
        return $this->respondWithItem($package, $statusCode, $headers);
    }

    public function respondWithStatus($statusCode, $headers = [])
    {
        return response()->make('', $statusCode, array_merge($this->getDefaultHeaders(), $headers));
    }

    public function respondWithError($message, $statusCode = 500, $headers = [])
    {
        return response()->make($message, $statusCode, array_merge($this->getDefaultHeaders(), $headers));
    }
}
