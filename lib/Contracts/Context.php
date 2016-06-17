<?php
/**
 * Clock Hour Management System
 *
 * @copyright Copyright (c) 2016 Puget Sound Educational Service District
 * @license   MIT
 */
namespace CHMS\Common\Contracts;

interface Context
{
    /**
     * Returns the fields and values from this context
     *
     * @return array
     */
    public function getContextFields();
}
