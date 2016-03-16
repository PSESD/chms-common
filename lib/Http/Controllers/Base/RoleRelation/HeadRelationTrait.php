<?php
/**
 * Clock Hour Management System
 *
 * @copyright Copyright (c) 2016 Puget Sound Educational Service District
 * @license   MIT
 */
namespace CHMS\Common\Http\Controllers\Base\RoleRelation;

use Illuminate\Http\Request;
use League\Fractal\Manager as Fractal;
use League\Fractal\Resource\Item as FractalItem;

trait HeadRelationTrait
{
    /**
     * Check the object relationship's existence
     *
     * @param  Request  $request        Request object
     * @param  string   $objectId       Unique identifier for object
     * @param  int      $relationId     Unique identifier for the UserRole relationship
     * @return ResponseInterface Response for object
     * @todo  implement
     */
    public function headRelationship(Request $request, Fractal $fractal, $objectId, $relationId)
    {
    }
}