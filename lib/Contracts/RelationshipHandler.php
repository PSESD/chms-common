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

interface RelationshipHandler
{
    /**
     * Handle relationship data
     * @param  ValidatedRelationshipData    $model
     * @param  array    $input
     * @param  string   $scope  Usually either create or update
     * @param  boolean   $throwException
     * @return boolean
     */
    protected function handle(Model $model, array $input, $scope, $throwException = true);


    /**
     * Validate relationship data
     * @param  Model    $model
     * @param  array    $input
     * @param  string   $scope  Usually either create or update
     * @return boolean
     */
    protected function validate(Model $model, array $input, $scope);
}
