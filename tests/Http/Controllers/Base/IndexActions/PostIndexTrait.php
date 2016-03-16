<?php
/**
 * Clock Hour Management System
 *
 * @copyright Copyright (c) 2016 Puget Sound Educational Service District
 * @license   MIT
 */
namespace CHMSTests\Common\Http\Controllers\Base\IndexActions;

trait PostIndexTrait
{
    public function testStandardPost()
    {
        $user = $this->getSuperAdmin();
        $generic = $this->getGeneric(true);
        $this->actingAs($user)->withoutMiddleware()->json('POST', $this->getRoute(), ['data' => ['attributes' => $generic]]);
        $this->seeJson($this->getExpected($generic));
    }
}