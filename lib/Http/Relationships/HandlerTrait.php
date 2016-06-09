<?php
/**
 * Clock Hour Management System
 *
 * @copyright Copyright (c) 2016 Puget Sound Educational Service District
 * @license   MIT
 */
namespace CHMS\Common\Http\Relationships;


use CHMS\Common\Http\Relationships\ValidatedRelationshipData;
use CHMS\Common\Exceptions\UnprocessableEntityHttpException;

trait BaseHandler
{
    protected function throwValidationException($errors)
    {
        throw new UnprocessableEntityHttpException("Unable to validate relationship input", $errors);
    }
}