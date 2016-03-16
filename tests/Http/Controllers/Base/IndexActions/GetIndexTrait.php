<?php
/**
 * Clock Hour Management System
 *
 * @copyright Copyright (c) 2016 Puget Sound Educational Service District
 * @license   MIT
 */
namespace CHMSTests\Common\Http\Controllers\Base\IndexActions;

use Illuminate\Http\Request;
use League\Fractal\Manager as Fractal;
use League\Fractal\Resource\Collection as FractalCollection;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;

trait GetIndexTrait
{
    public function testStandardGet()
    {
        $user = $this->getSuperAdmin();
        $fake = $this->createOne();
        $this->actingAs($user)->withoutMiddleware()->get($this->getRoute());
        $this->seeJson(['id' => $fake->id]);
    }
}