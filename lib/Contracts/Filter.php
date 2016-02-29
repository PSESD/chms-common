<?php
/**
 * Clock Hour Management System
 *
 * @copyright Copyright (c) 2016 Puget Sound Educational Service District
 * @license   MIT
 */
namespace CHMS\Common\Contracts;

use CHMS\Common\Exceptions\InvalidInputException;
use Illuminate\Database\Eloquent\Model;

interface Filter
{
    /**
     * Filter input
     * @param  Model    $model
     * @param  array    $input
     * @param  string   $scope  Usually either create or update
     * @param  boolean  $throwException  
     * @return array
     * @throws InvalidInputException when input is given that isn't allowed
     */
    public function filter(Model $model, array $input, $scope, $throwException = true);
}
