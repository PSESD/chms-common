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
use League\Fractal\Scope as Fractal;

abstract class Controller extends BaseController
{
    public function __construct()
    {
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
