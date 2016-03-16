<?php
/**
 * Clock Hour Management System
 *
 * @copyright Copyright (c) 2016 Puget Sound Educational Service District
 * @license   MIT
 */
namespace CHMS\Common\Http\Controllers\Base;

use League\Fractal\TransformerAbstract;

interface ControllerInterface
{
    /**
     * Get the object model class for this controller
     *
     * @return Repository object
     */
    public function getRepository();

    /**
     * Gets the transformer for the object
     *
     * @return TransformerAbstract Tranformer object
     */
    public function getTransformer();

    /**
     * Gets the resource key for the object
     *
     * @return string Resource key
     */
    public function getResourceKey();
}
